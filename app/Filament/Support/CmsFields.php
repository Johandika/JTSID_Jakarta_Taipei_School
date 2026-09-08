<?php

namespace App\Filament\Support;

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
    public static function translations(bool $menu = false): Repeater
    {
        $schema = [
            Select::make('locale')->options(['en' => 'English', 'zh-TW' => 'Traditional Chinese', 'id' => 'Indonesian'])->required()->distinct(),
            TextInput::make('title')->required()->maxLength(191),
        ];
        if (! $menu) {
            $schema = array_merge($schema, [
                Textarea::make('summary')->rows(2)->maxLength(1000),
                MarkdownEditor::make('body')->toolbarButtons(['bold', 'italic', 'heading', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo'])->columnSpanFull(),
                TextInput::make('seo_title')->maxLength(191),
                Textarea::make('seo_description')->maxLength(255)->rows(2),
            ]);
        }

        return Repeater::make('translations')->relationship()->schema($schema)->columns(2)
            ->default([['locale' => 'en']])->minItems(1)->maxItems(3)->reorderable(false)
            ->itemLabel(fn (array $state): string => ($state['locale'] ?? 'New').' - '.($state['title'] ?? 'Translation'))
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (! collect($value)->contains(fn ($row) => ($row['locale'] ?? '') === 'en' && filled($row['title'] ?? null))) {
                    $fail('An English translation is required.');
                }
            }])->columnSpanFull();
    }

    public static function publishing(): Section
    {
        return Section::make('Publishing')->schema([
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->default('draft')->required(),
            DateTimePicker::make('published_at')->label('Publish date')->seconds(false),
        ])->columns(2);
    }

    public static function department(): Select
    {
        return Select::make('department_id')->relationship('department', 'name',
            fn (Builder $query) => auth()->user()?->role?->code === 'department_admin' ? $query->whereKey(auth()->user()->department_id) : $query)
            ->default(fn () => auth()->user()?->department_id)->required()->searchable()->preload()
            ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                if (auth()->user()?->role?->code === 'department_admin' && (int) $value !== (int) auth()->user()->department_id) {
                    $fail('Select your own department.');
                }
            }]);
    }

    public static function image(string $name = 'image', int $max = 4096): FileUpload
    {
        return FileUpload::make($name)->disk('public')->directory('images')->visibility('public')
            ->preventFilePathTampering()
            ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize($max)->imageEditor()
            ->rules(['extensions:jpg,jpeg,png,webp']);
    }

    public static function document(bool $required = false): FileUpload
    {
        return FileUpload::make('file_path')->label('Document')->disk('local')->directory('documents')
            ->preventFilePathTampering()
            ->visibility('private')->acceptedFileTypes(['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.rar', 'application/x-rar', 'application/x-rar-compressed'])
            ->maxSize(15360)->required($required)->rules(['extensions:pdf,doc,docx,rar']);
    }

    public static function uploadDate(): DatePicker
    {
        return DatePicker::make('upload_date')->required()->default(today());
    }

    public static function safeUrl(string $name): TextInput
    {
        return TextInput::make($name)->maxLength(255)->rules(['regex:~^(?:/(?!/)[^\\s]*|https?://[^\\s]+)$~']);
    }
}
