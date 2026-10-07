<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\HeroSlide;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlideResource extends CmsResource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Hero Slides';

    protected static ?string $pluralModelLabel = 'Hero Slides';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make(__('admin.sections.hero_media'))
                        ->description(__('admin.descriptions.hero_media'))
                        ->schema([
                            CmsFields::image('image', 4096, __('admin.labels.hero_image'))
                                ->label(__('admin.labels.hero_slide_image'))
                                ->helperText(__('admin.helpers.hero_image'))
                                ->rules([
                                    fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (blank($value) && blank($get('video_url'))) {
                                            $fail(__('admin.validation.hero_image_or_youtube'));
                                        }
                                    },
                                ]),

                            TextInput::make('video_url')
                                ->label(__('admin.labels.video_url'))
                                ->placeholder(__('admin.placeholders.video_url'))
                                ->maxLength(500)
                                ->helperText(__('admin.helpers.video_url'))
                                ->url()
                                ->rules([
                                    fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (blank($value) && blank($get('image'))) {
                                            $fail(__('admin.validation.hero_image_or_video'));
                                        }
                                    },
                                ]),
                        ]),

                    Section::make(__('admin.sections.hero_text_panel'))
                        ->description(__('admin.descriptions.hero_text_panel'))
                        ->schema([
                            Toggle::make('show_text')
                                ->label(__('admin.labels.show_hero_text_panel'))
                                ->default(true)
                                ->live()
                                ->helperText(__('admin.helpers.show_hero_text_panel')),

                            Tabs::make('SlideTranslations')
                                ->visible(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                                ->tabs([
                                    Tab::make('en')
                                        ->label('🇬🇧 English (Primary)')
                                        ->schema([
                                            TextInput::make('tagline.en')
                                                ->label('Tagline / Eyebrow (EN)')
                                                ->placeholder('e.g. Jakarta Taipei School')
                                                ->maxLength(100),
                                            TextInput::make('title.en')
                                                ->label(__('admin.labels.main_headline_en'))
                                                ->placeholder('e.g. Excellence in Trilingual Education')
                                                ->required(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                                                ->maxLength(191),
                                            Textarea::make('description.en')
                                                ->label('Deskripsi Singkat (EN)')
                                                ->placeholder('Brief introduction or summary...')
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.en')
                                                ->label('Teks Tombol CTA (EN)')
                                                ->placeholder('e.g. Apply Now')
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                        ->schema([
                                            TextInput::make('tagline.id')
                                                ->label('Tagline / Eyebrow (ID)')
                                                ->placeholder('cth. Jakarta Taipei School')
                                                ->maxLength(100),
                                            TextInput::make('title.id')
                                                ->label(__('admin.labels.main_headline_id'))
                                                ->placeholder(__('admin.placeholders.hero_headline'))
                                                ->maxLength(191),
                                            Textarea::make('description.id')
                                                ->label(__('admin.labels.short_description_id'))
                                                ->placeholder(__('admin.placeholders.short_description'))
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.id')
                                                ->label(__('admin.labels.cta_text_id'))
                                                ->placeholder(__('admin.placeholders.cta_text'))
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Traditional Chinese (繁體中文)')
                                        ->schema([
                                            TextInput::make('tagline.zh-TW')
                                                ->label('Tagline / Eyebrow (中文)')
                                                ->placeholder('例：雅加達臺北學校')
                                                ->maxLength(100),
                                            TextInput::make('title.zh-TW')
                                                ->label(__('admin.labels.main_headline_zh'))
                                                ->placeholder('例：深耕三語，開創卓越教育')
                                                ->maxLength(191),
                                            Textarea::make('description.zh-TW')
                                                ->label('Deskripsi Singkat (中文)')
                                                ->placeholder('簡短介紹或摘要...')
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.zh-TW')
                                                ->label('Teks Tombol CTA (中文)')
                                                ->placeholder('例：立即報名')
                                                ->maxLength(100),
                                        ]),
                                ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.cta_link'))
                        ->description(__('admin.descriptions.cta_link'))
                        ->visible(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                        ->schema([
                            CmsFields::safeUrl('cta_url', __('admin.labels.cta_url_target'))
                                ->label(__('admin.labels.cta_url'))
                                ->placeholder(__('admin.placeholders.cta_url'))
                                ->helperText(__('admin.helpers.cta_url')),
                        ]),

                    Section::make(__('admin.sections.status_display_order'))
                        ->schema([
                            TextInput::make('sort_order')
                                ->label(__('admin.fields.display_order'))
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required()
                                ->helperText(__('admin.helpers.carousel_order')),
                            Toggle::make('is_active')
                                ->label(__('admin.labels.active_show_website'))
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('admin.labels.photo_poster'))
                    ->state(fn (HeroSlide $record): ?string => $record->getImageOrPosterUrl())
                    ->width(80)
                    ->height(50),
                TextColumn::make('title.en')
                    ->label(__('admin.labels.headline_en'))
                    ->searchable()
                    ->weight('medium')
                    ->placeholder(__('admin.placeholders.no_text_full_media'))
                    ->limit(40),
                IconColumn::make('video_url')
                    ->label(__('admin.labels.video'))
                    ->boolean()
                    ->state(fn (HeroSlide $record): bool => $record->hasVideo())
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('show_text')
                    ->label(__('admin.labels.shape_text'))
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-bottom-center-text')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('tagline.en')
                    ->label(__('admin.labels.tagline'))
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
