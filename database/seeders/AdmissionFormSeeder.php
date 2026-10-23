<?php

namespace Database\Seeders;

use App\Models\AdmissionForm;
use App\Support\SchoolLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AdmissionFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SchoolLevel::OPTIONS as $code => $label) {
            $path = "admission-forms/form-pendaftaran-jts-{$code}.pdf";

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $this->sampleAdmissionFormPdf($label));
            }

            $form = AdmissionForm::firstOrNew(['school_level' => $code, 'nationality' => 'wni']);

            if (! $form->exists) {
                $form->fill([
                    'title' => "Form Pendaftaran JTS {$label}",
                    'file_path' => $path,
                    'is_active' => true,
                ]);
            } else {
                $form->title = $form->title ?: "Form Pendaftaran JTS {$label}";
                $form->file_path = $form->file_path ?: $path;
                $form->is_active = $form->is_active ?? true;
            }

            $form->save();
        }
    }

    private function sampleAdmissionFormPdf(string $schoolLevel): string
    {
        $stream = "BT /F1 20 Tf 60 740 Td (Jakarta Taipei School) Tj 0 -40 Td /F1 14 Tf (FORM PENDAFTARAN SISWA BARU {$schoolLevel}) Tj 0 -35 Td /F1 11 Tf (Silakan ganti file ini melalui Admin > Admissions > Form Pendaftaran.) Tj ET";
        $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>', '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream"];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }
}
