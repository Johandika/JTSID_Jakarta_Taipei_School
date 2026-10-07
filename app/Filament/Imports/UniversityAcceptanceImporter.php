<?php

namespace App\Filament\Imports;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\UniversityAcceptance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use League\Csv\Info;
use League\Csv\Reader;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class UniversityAcceptanceImporter
{
    /**
     * @var array<int, string>
     */
    public const TEMPLATE_HEADERS = [
        'student_name',
        'university_name',
        'program_name',
        'admission_path',
        'country',
        'academic_year',
        'department',
        'image_url',
        'sort_order',
        'is_active',
    ];

    /**
     * @var array<int, array<int, string|int>>
     */
    public const TEMPLATE_ROWS = [
        [
            'Sample Student 1',
            'National Taiwan University',
            'Computer Science',
            'International Admission',
            'Taiwan',
            '2026 / 2027',
            'Academic Office',
            'https://drive.google.com/file/d/your-file-id/view?usp=sharing',
            1,
            1,
        ],
        [
            'Sample Student 2',
            'Universitas Indonesia',
            'Medicine',
            'SNBP',
            'Indonesia',
            '2026 / 2027',
            'Academic Office',
            '',
            2,
            1,
        ],
    ];

    public static function writeTemplateToPath(string $path): void
    {
        $writer = new XlsxWriter();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(self::TEMPLATE_HEADERS));

        foreach (self::TEMPLATE_ROWS as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();
    }

    /**
     * @return array{created: int, updated: int}
     *
     * @throws ValidationException
     */
    public static function importFromPath(string $path, ?string $extension = null): array
    {
        if (strtolower((string) $extension) === 'xlsx') {
            return self::importFromXlsxPath($path);
        }

        $stream = fopen($path, 'r');

        try {
            return self::importFromStream($stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * @param  resource  $stream
     * @return array{created: int, updated: int}
     *
     * @throws ValidationException
     */
    public static function importFromStream($stream): array
    {
        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(self::guessDelimiter($csv));
        $csv->setHeaderOffset(0);

        $headers = array_map(self::normalizeHeader(...), $csv->getHeader());
        $missingHeaders = array_diff(self::TEMPLATE_HEADERS, $headers);

        if ($missingHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => __('admin.imports.missing_headers', ['headers' => implode(', ', $missingHeaders)]),
            ]);
        }

        return self::importRecords($csv->getRecords());
    }

    /**
     * @return array{created: int, updated: int}
     *
     * @throws ValidationException
     */
    private static function importFromXlsxPath(string $path): array
    {
        $reader = new XlsxReader();
        $reader->open($path);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $header = null;
                $records = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $values = array_map(
                        static fn (mixed $value): string => trim((string) $value),
                        $row->toArray(),
                    );

                    if (self::isEmptyRow($values)) {
                        continue;
                    }

                    if ($header === null) {
                        $header = array_map(self::normalizeHeader(...), $values);

                        continue;
                    }

                    $records[] = array_combine(
                        $header,
                        array_slice(array_pad($values, count($header), ''), 0, count($header)),
                    );
                }

                return self::importRecords($records, $header ?? []);
            }
        } finally {
            $reader->close();
        }

        return self::importRecords([], []);
    }

    /**
     * @param  iterable<array<string, mixed>>  $records
     * @param  array<int, string>|null  $headers
     * @return array{created: int, updated: int}
     *
     * @throws ValidationException
     */
    private static function importRecords(iterable $records, ?array $headers = null): array
    {
        if ($headers !== null) {
            $missingHeaders = array_diff(self::TEMPLATE_HEADERS, $headers);

            if ($missingHeaders !== []) {
                throw ValidationException::withMessages([
                    'file' => __('admin.imports.missing_headers', ['headers' => implode(', ', $missingHeaders)]),
                ]);
            }
        }

        $created = 0;
        $updated = 0;

        foreach ($records as $index => $row) {
            $rowNumber = $index + 2;
            $data = self::normalizeRow($row);

            Validator::make(
                $data,
                [
                    'student_name' => ['required', 'string', 'max:255'],
                    'university_name' => ['required', 'string', 'max:255'],
                    'program_name' => ['nullable', 'string', 'max:255'],
                    'admission_path' => ['nullable', 'string', 'max:255'],
                    'country' => ['nullable', 'string', 'max:100'],
                    'academic_year' => ['required', 'string', 'max:50'],
                    'department' => [
                        'required',
                        'string',
                        'max:100',
                        fn (string $attribute, mixed $value, \Closure $fail): mixed => self::resolveDepartmentId($value) !== null
                            ? null
                            : $fail(__('admin.imports.department_not_found')),
                    ],
                    'image_url' => ['nullable', 'url', 'max:2048'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'is_active' => ['nullable', 'boolean'],
                ],
                [],
                [
                    'student_name' => __('admin.labels.student_name'),
                    'university_name' => __('admin.labels.university_name'),
                    'academic_year' => __('admin.fields.academic_year'),
                    'department' => __('admin.fields.department'),
                ],
            )->validateWithBag('Row '.$rowNumber);

            $academicYearId = self::resolveAcademicYearId($data['academic_year']);
            $departmentId = self::resolveDepartmentId($data['department']);
            $acceptance = UniversityAcceptance::firstOrNew([
                'student_name' => $data['student_name'],
                'university_name' => $data['university_name'],
                'academic_year_id' => $academicYearId,
            ]);
            $exists = $acceptance->exists;

            $acceptance->fill([
                'department_id' => $departmentId,
                'program_name' => $data['program_name'] ?: null,
                'admission_path' => $data['admission_path'] ?: null,
                'country' => $data['country'] ?: null,
                'image_url' => $data['image_url'] ?: null,
                'sort_order' => filled($data['sort_order']) ? (int) $data['sort_order'] : 0,
                'is_active' => self::castBoolean($data['is_active']),
            ]);
            $acceptance->save();

            $exists ? $updated++ : $created++;
        }

        return [
            'created' => $created,
            'updated' => $updated,
        ];
    }

    /**
     * @param  array<int, string>  $values
     */
    private static function isEmptyRow(array $values): bool
    {
        return implode('', $values) === '';
    }

    private static function guessDelimiter(Reader $csv): string
    {
        $delimiterCounts = Info::getDelimiterStats($csv, delimiters: [',', ';', '|', "\t"], limit: 10);

        return (string) array_search(max($delimiterCounts), $delimiterCounts, true);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, string>
     */
    private static function normalizeRow(array $row): array
    {
        $normalized = array_fill_keys(self::TEMPLATE_HEADERS, '');

        foreach ($row as $key => $value) {
            $normalized[self::normalizeHeader((string) $key)] = trim((string) $value);
        }

        return $normalized;
    }

    private static function normalizeHeader(string $header): string
    {
        return trim(strtolower(preg_replace('/^\xEF\xBB\xBF/', '', $header)));
    }

    private static function resolveAcademicYearId(mixed $state): int
    {
        $year = trim((string) $state);
        $code = str_replace(' / ', '-', $year);
        $code = str_replace(' ', '', $code);

        return AcademicYear::firstOrCreate(
            ['code' => $code],
            ['name' => str_replace('-', ' / ', $code)],
        )->id;
    }

    private static function resolveDepartmentId(mixed $state): ?int
    {
        $department = trim((string) $state);

        if ($department === '') {
            return null;
        }

        return Department::query()
            ->whereRaw('lower(code) = ?', [strtolower($department)])
            ->orWhereRaw('lower(name) = ?', [strtolower($department)])
            ->value('id');
    }

    private static function castBoolean(mixed $state): bool
    {
        return match (strtolower(trim((string) $state))) {
            '', '1', 'true', 'yes', 'y', 'on' => true,
            '0', 'false', 'no', 'n', 'off' => false,
            default => (bool) $state,
        };
    }
}
