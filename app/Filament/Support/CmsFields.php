<?php

namespace App\Filament\Support;

use App\Support\SchoolLevel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class CmsFields
{
    public static function schoolLevels(): Select
    {
        return Select::make('school_levels')
            ->label('Jenjang Sekolah')
            ->multiple()
            ->options(SchoolLevel::OPTIONS)
            ->default([])
            ->helperText('Kosongkan untuk seluruh sekolah, atau pilih: TK, SD, SMP, SMA.')
            ->rules(['array'])
            ->columnSpanFull();
    }

    public static function translations(string $titleLabel = 'Judul Konten'): Repeater
    {
        $schema = [
            Select::make('locale')
                ->label('Bahasa')
                ->options([
                    'en' => 'English (Primary)',
                    'zh-TW' => 'Traditional Chinese (繁體中文)',
                    'id' => 'Indonesian (Bahasa Indonesia)',
                ])
                ->required()
                ->distinct(),
            TextInput::make('title')
                ->label($titleLabel)
                ->required()
                ->maxLength(191),
            Textarea::make('summary')
                ->label('Ringkasan Singkat')
                ->rows(2)
                ->maxLength(1000)
                ->columnSpanFull(),
            MarkdownEditor::make('body')
                ->label('Isi Konten')
                ->toolbarButtons(['bold', 'italic', 'heading', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo'])
                ->columnSpanFull(),
            Section::make('SEO & Metadata Search Engine')
                ->description('Pengaturan meta title dan description untuk mesin pencari (Opsional)')
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Meta Title')
                        ->maxLength(191),
                    Textarea::make('seo_description')
                        ->label('SEO Meta Description')
                        ->maxLength(255)
                        ->rows(2),
                ])
                ->collapsed()
                ->columnSpanFull(),
        ];

        return Repeater::make('translations')
            ->label('Konten Multi-Bahasa')
            ->relationship()
            ->schema($schema)
            ->columns(2)
            ->default([['locale' => 'en']])
            ->minItems(1)
            ->maxItems(3)
            ->reorderable(false)
            ->collapsible()
            ->itemLabel(fn (array $state): string => match ($state['locale'] ?? '') {
                'en' => '🇬🇧 English (Required Primary)',
                'zh-TW' => '🇹🇼 Traditional Chinese (繁體中文)',
                'id' => '🇮🇩 Indonesian (Bahasa Indonesia)',
                default => 'Terjemahan Baru',
            }.(filled($state['title'] ?? null) ? ' — '.$state['title'] : ''))
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (! collect($value)->contains(fn ($row) => ($row['locale'] ?? '') === 'en' && filled($row['title'] ?? null))) {
                    $fail('Terjemahan bahasa Inggris (English) wajib diisi.');
                }
            }])
            ->columnSpanFull();
    }

    public static function publishing(): Section
    {
        return Section::make('Status & Publikasi')
            ->description('Atur status tayang dan jadwal rilis')
            ->schema([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Jadwal Publikasi')
                    ->seconds(false)
                    ->helperText('Kosongkan untuk langsung tayang saat Published.'),
            ]);
    }

    public static function department(): Select
    {
        return Select::make('department_id')
            ->label('Departemen')
            ->relationship('department', 'name',
                fn (Builder $query) => auth()->user()?->role?->code === 'department_admin' ? $query->whereKey(auth()->user()->department_id) : $query)
            ->default(fn () => auth()->user()?->department_id)
            ->required()
            ->searchable()
            ->preload()
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (auth()->user()?->role?->code === 'department_admin' && (int) $value !== (int) auth()->user()->department_id) {
                    $fail('Pilih departemen Anda sendiri.');
                }
            }]);
    }

    public static function image(string $name = 'image', int $max = 4096, ?string $label = 'Gambar / Foto Cover'): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('public')
            ->directory('images')
            ->visibility('public')
            ->preventFilePathTampering()
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize($max)
            ->imageEditor()
            ->rules(['extensions:jpg,jpeg,png,webp']);
    }

    public static function document(bool $required = false, ?string $label = 'File Dokumen'): FileUpload
    {
        return FileUpload::make('file_path')
            ->label($label)
            ->disk('local')
            ->directory('documents')
            ->preventFilePathTampering()
            ->visibility('private')
            ->acceptedFileTypes(['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.rar', 'application/x-rar', 'application/x-rar-compressed'])
            ->maxSize(15360)
            ->required($required)
            ->rules(['extensions:pdf,doc,docx,rar'])
            ->helperText('Format didukung: PDF, DOCX, RAR (Maks. 15MB)');
    }

    public static function uploadDate(): DatePicker
    {
        return DatePicker::make('upload_date')
            ->label('Tanggal Unggah')
            ->required()
            ->default(today());
    }

    public static function safeUrl(string $name, ?string $label = 'Link URL Tujuan'): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->placeholder('https:// atau /path-internal')
            ->maxLength(255)
            ->rules(['regex:~^(?:/(?!/)[^\\s]*|https?://[^\\s]+)$~']);
    }
}
