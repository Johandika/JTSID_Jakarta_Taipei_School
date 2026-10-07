<?php

namespace App\Filament\Resources\UniversityAcceptances\Pages;

use App\Filament\Imports\UniversityAcceptanceImporter;
use App\Filament\Resources\UniversityAcceptances\UniversityAcceptanceResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListUniversityAcceptances extends ListRecords
{
    protected static string $resource = UniversityAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadImportTemplate')
                ->label(__('admin.actions.download_csv_template'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => response()->streamDownload(function (): void {
                    $path = tempnam(sys_get_temp_dir(), 'university-acceptances-template-');

                    try {
                        UniversityAcceptanceImporter::writeTemplateToPath($path);
                        readfile($path);
                    } finally {
                        if (is_string($path) && file_exists($path)) {
                            unlink($path);
                        }
                    }
                }, 'university-acceptances-import-template.xlsx', [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])),
            Action::make('importCsv')
                ->label(__('admin.actions.import_csv'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('admin.labels.import_university_acceptances'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('admin.labels.csv_file'))
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/x-csv',
                            'application/csv',
                            'application/x-csv',
                            'text/comma-separated-values',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->storeFiles(false)
                        ->visibility('private')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    /** @var TemporaryUploadedFile $file */
                    $file = $data['file'];
                    $result = UniversityAcceptanceImporter::importFromPath(
                        $file->getRealPath(),
                        $file->getClientOriginalExtension(),
                    );

                    Notification::make()
                        ->title(__('admin.messages.import_csv_finished'))
                        ->body(__('admin.messages.import_csv_result', ['created' => $result['created'], 'updated' => $result['updated']]))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
