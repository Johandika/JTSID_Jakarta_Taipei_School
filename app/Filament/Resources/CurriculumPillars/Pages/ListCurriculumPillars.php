<?php

namespace App\Filament\Resources\CurriculumPillars\Pages;

use App\Filament\Resources\CurriculumPillars\CurriculumPillarResource;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ListCurriculumPillars extends ListRecords
{
    protected static string $resource = CurriculumPillarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editHeader')
                ->label(__('admin.curriculum_pillars.actions.edit_header'))
                ->icon('heroicon-m-pencil-square')
                ->color('gray')
                ->modalHeading(__('admin.curriculum_pillars.modal.heading'))
                ->modalDescription(__('admin.curriculum_pillars.modal.description'))
                ->fillForm(function (): array {
                    $setting = SiteSetting::get('academic_curriculum_header', []);

                    return [
                        'eyebrow' => [
                            'en' => $setting['eyebrow']['en'] ?? 'Three Curriculum Pillars',
                            'id' => $setting['eyebrow']['id'] ?? 'Tiga Pilar Kurikulum',
                            'zh-TW' => $setting['eyebrow']['zh-TW'] ?? '三大課程支柱',
                        ],
                        'heading' => [
                            'en' => $setting['heading']['en'] ?? 'One Education, Three Excellences',
                            'id' => $setting['heading']['id'] ?? 'Satu Pendidikan, Tiga Keunggulan',
                            'zh-TW' => $setting['heading']['zh-TW'] ?? '一個教育，三大優勢',
                        ],
                        'subheading' => [
                            'en' => $setting['subheading']['en'] ?? 'JTS curriculum combines the strengths of Taiwan, Indonesia, and Cambridge English education.',
                            'id' => $setting['subheading']['id'] ?? 'Kurikulum JTS menggabungkan kekuatan pendidikan Taiwan, Indonesia, dan Cambridge English.',
                            'zh-TW' => $setting['subheading']['zh-TW'] ?? '雅加達臺灣學校課程融合臺灣教育、印尼國家課綱與劍橋英語三大優勢。',
                        ],
                    ];
                })
                ->form([
                    Tabs::make('HeaderTranslations')
                        ->tabs([
                            Tab::make('en')
                                ->label(__('admin.curriculum_pillars.tabs.en'))
                                ->schema([
                                    TextInput::make('eyebrow.en')
                                        ->label(__('admin.curriculum_pillars.fields.eyebrow_en'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.eyebrow_placeholder_en'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.en')
                                        ->label(__('admin.curriculum_pillars.fields.main_heading_en'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.main_heading_placeholder_en'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.en')
                                        ->label(__('admin.curriculum_pillars.fields.subheading_en'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.subheading_placeholder_en'))
                                        ->rows(3)
                                        ->maxLength(500),
                                ]),
                            Tab::make('id')
                                ->label(__('admin.curriculum_pillars.tabs.id'))
                                ->schema([
                                    TextInput::make('eyebrow.id')
                                        ->label(__('admin.curriculum_pillars.fields.eyebrow_id'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.eyebrow_placeholder_id'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.id')
                                        ->label(__('admin.curriculum_pillars.fields.main_heading_id'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.main_heading_placeholder_id'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.id')
                                        ->label(__('admin.curriculum_pillars.fields.subheading_id'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.subheading_placeholder_id'))
                                        ->rows(3)
                                        ->maxLength(500),
                                ]),
                            Tab::make('zh-TW')
                                ->label(__('admin.curriculum_pillars.tabs.zh'))
                                ->schema([
                                    TextInput::make('eyebrow.zh-TW')
                                        ->label(__('admin.curriculum_pillars.fields.eyebrow_zh'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.eyebrow_placeholder_zh'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('heading.zh-TW')
                                        ->label(__('admin.curriculum_pillars.fields.main_heading_zh'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.main_heading_placeholder_zh'))
                                        ->required()
                                        ->maxLength(150),
                                    Textarea::make('subheading.zh-TW')
                                        ->label(__('admin.curriculum_pillars.fields.subheading_zh'))
                                        ->placeholder(__('admin.curriculum_pillars.fields.subheading_placeholder_zh'))
                                        ->rows(3)
                                        ->maxLength(500),
                                ]),
                        ]),
                ])
                ->action(function (array $data): void {
                    SiteSetting::set('academic_curriculum_header', $data);

                    Notification::make()
                        ->title(__('admin.curriculum_pillars.modal.notification_updated'))
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label(__('admin.curriculum_pillars.actions.create')),
        ];
    }
}
