<?php

namespace App\Filament\Resources\AdmissionForms\Schemas;

use App\Models\AdmissionForm;
use App\Support\SchoolLevel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionFormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('File Formulir Pendaftaran')
                    ->description('Upload satu file formulir yang akan muncul sebagai tombol download di halaman Admissions.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul File')
                            ->default('Form Pendaftaran')
                            ->required()
                            ->maxLength(191),
                        Select::make('school_level')
                            ->label('Jenjang')
                            ->options(SchoolLevel::OPTIONS)
                            ->default(fn (): string => collect(array_keys(SchoolLevel::OPTIONS))
                                ->first(fn (string $level): bool => ! AdmissionForm::where('school_level', $level)->exists(), 'tk'))
                            ->required()
                            ->unique(AdmissionForm::class, 'school_level', ignoreRecord: true)
                            ->rules(['in:tk,sd,smp,sma']),
                        FileUpload::make('file_path')
                            ->label('File Formulir Pendaftaran (PDF, DOCX, RAR)')
                            ->disk('public')
                            ->directory('admission-forms')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->maxSize(15360)
                            ->required()
                            ->rules(['extensions:pdf,doc,docx,rar'])
                            ->helperText('Format didukung: PDF, DOCX, RAR (Maks. 15MB)'),
                        Toggle::make('is_active')
                            ->label('Tampilkan di halaman Admissions')
                            ->default(true),
                    ]),
            ]);
    }
}
