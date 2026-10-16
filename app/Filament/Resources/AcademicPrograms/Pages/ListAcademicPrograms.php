<?php

namespace App\Filament\Resources\AcademicPrograms\Pages;

use App\Filament\Resources\AcademicPrograms\AcademicProgramResource;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ListAcademicPrograms extends ListRecords
{
    protected static string $resource = AcademicProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editHeader')
                ->label(__('admin.academic_programs.actions.edit_header'))
                ->icon('heroicon-m-pencil-square')
                ->color('gray')
                ->modalHeading(__('admin.academic_programs.modal.heading'))
                ->modalDescription(__('admin.academic_programs.modal.description'))
                ->fillForm(function (): array {
                    $setting = SiteSetting::get('home_academics_header', []);

                    return [
                        'eyebrow' => [
                            'en' => $setting['eyebrow']['en'] ?? 'Academics',
                            'id' => $setting['eyebrow']['id'] ?? 'Akademik',
                            'zh-TW' => $setting['eyebrow']['zh-TW'] ?? '學術課程',
                        ],
                        'heading' => [
                            'en' => $setting['heading']['en'] ?? 'Integrated Curriculum for Global Generations',
                            'id' => $setting['heading']['id'] ?? 'Kurikulum Terpadu untuk Generasi Global',
                            'zh-TW' => $setting['heading']['zh-TW'] ?? '培育全球世代的整合課程',
                        ],
                        'subheading' => [
                            'en' => $setting['subheading']['en'] ?? 'Combining the Taiwan Curriculum, multilingual education, and global perspectives to empower the development of every student.',
                            'id' => $setting['subheading']['id'] ?? 'Menggabungkan Kurikulum Taiwan, pembelajaran multibahasa, dan wawasan internasional untuk mendukung perkembangan setiap siswa.',
                            'zh-TW' => $setting['subheading']['zh-TW'] ?? '結合臺灣課程體系、多語教學與國際視野，全面支持每一位學生的多元潛能發展。',
                        ],
                        'cta_label' => [
                            'en' => $setting['cta_label']['en'] ?? 'Explore Curriculum',
                            'id' => $setting['cta_label']['id'] ?? 'Pelajari Kurikulum',
                            'zh-TW' => $setting['cta_label']['zh-TW'] ?? '了解課程體系',
                        ],
                    ];
                })
                ->form([
                    Tabs::make('HeaderTranslations')
                        ->tabs([
                            Tab::make('en')
                                ->label(__('admin.academic_programs.tabs.en'))
                                ->schema([
                                    TextInput::make('eyebrow.en')
                                        ->label(__('admin.academic_programs.fields.eyebrow_en'))
                                        ->placeholder(__('admin.academic_programs.fields.eyebrow_placeholder_en'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.en')
                                        ->label(__('admin.academic_programs.fields.main_heading_en'))
                                        ->placeholder(__('admin.academic_programs.fields.main_heading_placeholder_en'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.en')
                                        ->label(__('admin.academic_programs.fields.subheading_en'))
                                        ->placeholder(__('admin.academic_programs.fields.subheading_placeholder_en'))
                                        ->rows(3)
                                        ->maxLength(500),
                                    TextInput::make('cta_label.en')
                                        ->label(__('admin.academic_programs.fields.cta_label_en'))
                                        ->placeholder(__('admin.academic_programs.fields.cta_label_placeholder_en'))
                                        ->required()
                                        ->maxLength(100),
                                ]),
                            Tab::make('id')
                                ->label(__('admin.academic_programs.tabs.id'))
                                ->schema([
                                    TextInput::make('eyebrow.id')
                                        ->label(__('admin.academic_programs.fields.eyebrow_id'))
                                        ->placeholder(__('admin.academic_programs.fields.eyebrow_placeholder_id'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.id')
                                        ->label(__('admin.academic_programs.fields.main_heading_id'))
                                        ->placeholder(__('admin.academic_programs.fields.main_heading_placeholder_id'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.id')
                                        ->label(__('admin.academic_programs.fields.subheading_id'))
                                        ->placeholder(__('admin.academic_programs.fields.subheading_placeholder_id'))
                                        ->rows(3)
                                        ->maxLength(500),
                                    TextInput::make('cta_label.id')
                                        ->label(__('admin.academic_programs.fields.cta_label_id'))
                                        ->placeholder(__('admin.academic_programs.fields.cta_label_placeholder_id'))
                                        ->required()
                                        ->maxLength(100),
                                ]),
                            Tab::make('zh-TW')
                                ->label(__('admin.academic_programs.tabs.zh'))
                                ->schema([
                                    TextInput::make('eyebrow.zh-TW')
                                        ->label(__('admin.academic_programs.fields.eyebrow_zh'))
                                        ->placeholder(__('admin.academic_programs.fields.eyebrow_placeholder_zh'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.zh-TW')
                                        ->label(__('admin.academic_programs.fields.main_heading_zh'))
                                        ->placeholder(__('admin.academic_programs.fields.main_heading_placeholder_zh'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.zh-TW')
                                        ->label(__('admin.academic_programs.fields.subheading_zh'))
                                        ->placeholder(__('admin.academic_programs.fields.subheading_placeholder_zh'))
                                        ->rows(3)
                                        ->maxLength(500),
                                    TextInput::make('cta_label.zh-TW')
                                        ->label(__('admin.academic_programs.fields.cta_label_zh'))
                                        ->placeholder(__('admin.academic_programs.fields.cta_label_placeholder_zh'))
                                        ->required()
                                        ->maxLength(100),
                                ]),
                        ]),
                ])
                ->action(function (array $data): void {
                    SiteSetting::set('home_academics_header', $data);

                    Notification::make()
                        ->title(__('admin.academic_programs.modal.notification_updated'))
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label(__('admin.academic_programs.actions.create')),
        ];
    }
}
