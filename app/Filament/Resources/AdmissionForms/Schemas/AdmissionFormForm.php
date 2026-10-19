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
                Section::make(__('admin.sections.admission_form_file'))
                    ->description(__('admin.descriptions.admission_form_file'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('admin.labels.file_title'))
                            ->default(__('admin.defaults.admission_form'))
                            ->required()
                            ->maxLength(191),
                        Select::make('school_level')
                            ->label(__('admin.fields.school_level'))
                            ->options(SchoolLevel::OPTIONS)
                            ->default(fn (): string => collect(array_keys(SchoolLevel::OPTIONS))
                                ->first(fn (string $level): bool => ! AdmissionForm::where('school_level', $level)->exists(), 'tk'))
                            ->required()
                            ->unique(AdmissionForm::class, 'school_level', ignoreRecord: true)
                            ->rules(['in:'.implode(',', array_keys(SchoolLevel::OPTIONS))]),
                        FileUpload::make('file_path')
                            ->label(__('admin.labels.admission_form_file'))
                            ->disk('public')
                            ->directory('admission-forms')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->maxSize(15360)
                            ->required()
                            ->rules(['extensions:pdf,doc,docx,rar'])
                            ->helperText(__('admin.helpers.document_format')),
                        Toggle::make('is_active')
                            ->label(__('admin.labels.show_on_admissions'))
                            ->default(true),
                    ]),
            ]);
    }
}
