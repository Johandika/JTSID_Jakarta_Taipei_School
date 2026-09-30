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
                ->label('Download CSV Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => response()->streamDownload(function (): void {
                    $handle = fopen('php://output', 'w');
                    fwrite($handle, "\xEF\xBB\xBF");

                    fputcsv($handle, UniversityAcceptanceImporter::TEMPLATE_HEADERS, separator: ';');

                    foreach (UniversityAcceptanceImporter::TEMPLATE_ROWS as $row) {
                        fputcsv($handle, $row, separator: ';');
                    }

                    fclose($handle);
                }, 'university-acceptances-import-template.csv', [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ])),
            Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Import University Acceptances')
                ->schema([
                    FileUpload::make('file')
                        ->label('CSV File')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/x-csv',
                            'application/csv',
                            'application/x-csv',
                            'text/comma-separated-values',
                            'text/plain',
                            'application/vnd.ms-excel',
                        ])
                        ->storeFiles(false)
                        ->visibility('private')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    /** @var TemporaryUploadedFile $file */
                    $file = $data['file'];
                    $stream = $file->readStream();

                    try {
                        $result = UniversityAcceptanceImporter::importFromStream($stream);
                    } finally {
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }

                    Notification::make()
                        ->title('Import CSV selesai')
                        ->body($result['created'].' data baru dibuat, '.$result['updated'].' data diperbarui.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
