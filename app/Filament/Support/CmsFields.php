<?php

namespace App\Filament\Support;

use App\Support\DepartmentPermission;
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
            ->label(__('admin.labels.school_levels'))
            ->multiple()
            ->options(SchoolLevel::OPTIONS)
            ->default([])
            ->helperText(__('admin.helpers.school_levels'))
            ->rules(['array'])
            ->columnSpanFull();
    }

    public static function translations(?string $titleLabel = null, bool|\Closure $showSummary = true, bool $showArticle = true): Repeater
    {
        $titleLabel ??= __('admin.labels.news_title');

        $schema = [
            Select::make('locale')
                ->label(__('admin.fields.language'))
                ->options([
                    'en' => __('admin.options.english_primary'),
                    'zh-TW' => __('admin.options.traditional_chinese'),
                    'id' => __('admin.options.indonesian'),
                ])
                ->required()
                ->distinct(),
            TextInput::make('title')
                ->label($titleLabel)
                ->required()
                ->maxLength(191),
            Textarea::make('summary')
                ->label(__('admin.dashboard.summary'))
                ->visible($showSummary)
                ->rows(2)
                ->maxLength(1000)
                ->columnSpanFull(),
            MarkdownEditor::make('body')
                ->label(__('admin.nav_groups.content'))
                ->visible($showArticle)
                ->toolbarButtons(['bold', 'italic', 'heading', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo'])
                ->columnSpanFull(),
            Section::make('SEO & Metadata Search Engine')
                ->visible($showArticle)
                ->description(__('admin.descriptions.seo_metadata'))
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
            ->label(__('admin.sections.content_translations'))
            ->relationship()
            ->schema($schema)
            ->columns(2)
            ->default([['locale' => 'en']])
            ->minItems(1)
            ->maxItems(3)
            ->reorderable(false)
            ->collapsible()
            ->itemLabel(fn (array $state): string => match ($state['locale'] ?? '') {
                'en' => 'English (Required Primary)',
                'zh-TW' => __('admin.options.traditional_chinese'),
                'id' => __('admin.options.indonesian'),
                default => __('admin.fields.translations'),
            }.(filled($state['title'] ?? null) ? ' — '.$state['title'] : ''))
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (! collect($value)->contains(fn ($row) => ($row['locale'] ?? '') === 'en' && filled($row['title'] ?? null))) {
                    $fail(__('admin.helpers.english_translation_required'));
                }
            }])
            ->columnSpanFull();
    }

    public static function publishing(): Section
    {
        return Section::make(__('admin.sections.publishing'))
            ->description(__('admin.descriptions.publishing'))
            ->schema([
                Select::make('status')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'draft' => __('admin.options.draft'),
                        'published' => __('admin.options.published'),
                        'archived' => __('admin.options.archived'),
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label(__('admin.labels.publishing_schedule'))
                    ->seconds(false)
                    ->helperText(__('admin.helpers.published_immediately')),
            ]);
    }

    public static function department(): Select
    {
        return Select::make('department_id')
            ->label(__('admin.fields.department'))
            ->relationship('department', 'name',
                fn (Builder $query) => (auth()->user() && DepartmentPermission::isDepartmentRole(auth()->user()->role?->code)) ? $query->whereKey(auth()->user()->department_id) : $query)
            ->default(fn () => auth()->user()?->department_id)
            ->required()
            ->searchable()
            ->preload()
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (auth()->user() && DepartmentPermission::isDepartmentRole(auth()->user()->role?->code) && (int) $value !== (int) auth()->user()->department_id) {
                    $fail(__('admin.helpers.own_department_only'));
                }
            }]);
    }

    public static function image(string $name = 'image', int $max = 4096, ?string $label = null): FileUpload
    {
        return FileUpload::make($name)
            ->label($label ?? __('admin.labels.image_cover'))
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

    public static function document(bool $required = false, ?string $label = null): FileUpload
    {
        return FileUpload::make('file_path')
            ->label($label ?? __('admin.fields.document_file'))
            ->disk('local')
            ->directory('documents')
            ->preventFilePathTampering()
            ->visibility('private')
            ->acceptedFileTypes(['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.rar', 'application/x-rar', 'application/x-rar-compressed'])
            ->maxSize(15360)
            ->required($required)
            ->rules(['extensions:pdf,doc,docx,rar'])
            ->helperText(__('admin.helpers.document_format'));
    }

    public static function uploadDate(): DatePicker
    {
        return DatePicker::make('upload_date')
            ->label(__('admin.fields.upload_date'))
            ->required()
            ->default(today());
    }

    public static function safeUrl(string $name, ?string $label = null): TextInput
    {
        return TextInput::make($name)
            ->label($label ?? __('admin.labels.read_more_link'))
            ->placeholder(__('admin.helpers.url_format'))
            ->maxLength(255)
            ->rules(['regex:~^(?:/(?!/)[^\\s]*|https?://[^\\s]+)$~']);
    }
}
