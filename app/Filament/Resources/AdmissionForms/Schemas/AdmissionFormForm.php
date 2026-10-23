<?php

namespace App\Filament\Resources\AdmissionForms\Schemas;

use App\Models\AdmissionForm;
use App\Support\SchoolLevel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

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
                            ->default(fn (): string => AdmissionForm::nextAvailableSlot()['school_level'] ?? 'tk')
                            ->required()
                            ->unique(AdmissionForm::class, 'school_level', ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('nationality', $get('nationality')))
                            ->rules(['in:'.implode(',', array_keys(SchoolLevel::OPTIONS))]),
                        Select::make('nationality')
                            ->label(__('admin.fields.nationality'))
                            ->options(AdmissionForm::nationalityOptions())
                            ->default(fn (): string => AdmissionForm::nextAvailableSlot()['nationality'] ?? 'wni')
                            ->required()
                            ->rules(['in:'.implode(',', AdmissionForm::NATIONALITIES)]),
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
