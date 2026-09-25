<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\CmsResource;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends CmsResource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Pesan Kontak';

    protected static ?string $pluralModelLabel = 'Pesan Kontak';

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?int $navigationSort = 9;

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::where('status', ContactMessage::STATUS_UNREAD)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Pengirim')
                        ->description('Data diri dan kontak pengirim pesan')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->readOnly(),

                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->readOnly(),

                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->readOnly()
                                ->placeholder('—'),

                            Select::make('subject')
                                ->label('Subjek / Kategori')
                                ->options([
                                    ContactMessage::SUBJECT_ADMISSIONS => 'Penerimaan Siswa Baru (Admissions)',
                                    ContactMessage::SUBJECT_MANDARIN_PROGRAM => 'Program Mandarin / TOCFL',
                                    ContactMessage::SUBJECT_GENERAL => 'Pertanyaan Umum',
                                    ContactMessage::SUBJECT_ACADEMICS => 'Akademik & Kurikulum',
                                    ContactMessage::SUBJECT_OTHER => 'Lainnya',
                                ])
                                ->disabled(),
                        ])
                        ->columns(2),

                    Section::make('Isi Pesan')
                        ->schema([
                            Textarea::make('message')
                                ->label('Pesan yang Dikirim')
                                ->rows(7)
                                ->readOnly(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Status & Tindak Lanjut')
                        ->description('Ubah status pesan dan beri catatan internal')
                        ->schema([
                            Select::make('status')
                                ->label('Status Pesan')
                                ->options([
                                    ContactMessage::STATUS_UNREAD => 'Belum Dibaca',
                                    ContactMessage::STATUS_READ => 'Sudah Dibaca',
                                    ContactMessage::STATUS_REPLIED => 'Sudah Dibalas',
                                    ContactMessage::STATUS_ARCHIVED => 'Diarsipkan',
                                ])
                                ->required(),

                            Textarea::make('admin_notes')
                                ->label('Catatan Admin')
                                ->placeholder('Tulis catatan tindak lanjut internal di sini...')
                                ->rows(4),

                            DateTimePicker::make('created_at')
                                ->label('Waktu Dikirim')
                                ->readOnly(),

                            TextInput::make('ip_address')
                                ->label('Alamat IP')
                                ->readOnly(),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ContactMessage::STATUS_UNREAD => 'warning',
                        ContactMessage::STATUS_READ => 'info',
                        ContactMessage::STATUS_REPLIED => 'success',
                        ContactMessage::STATUS_ARCHIVED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (ContactMessage $record): string => $record->getStatusLabel()),

                TextColumn::make('name')
                    ->label('Pengirim')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (ContactMessage $record): ?string => $record->phone),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Alamat email disalin'),

                TextColumn::make('subject')
                    ->label('Subjek')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (ContactMessage $record): string => $record->getSubjectLabel()),

                TextColumn::make('message')
                    ->label('Cuplikan Pesan')
                    ->limit(45)
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesan')
                    ->options([
                        ContactMessage::STATUS_UNREAD => 'Belum Dibaca',
                        ContactMessage::STATUS_READ => 'Sudah Dibaca',
                        ContactMessage::STATUS_REPLIED => 'Sudah Dibalas',
                        ContactMessage::STATUS_ARCHIVED => 'Diarsipkan',
                    ]),
                SelectFilter::make('subject')
                    ->label('Kategori Subjek')
                    ->options([
                        ContactMessage::SUBJECT_ADMISSIONS => 'Penerimaan Siswa Baru',
                        ContactMessage::SUBJECT_MANDARIN_PROGRAM => 'Program Mandarin / TOCFL',
                        ContactMessage::SUBJECT_GENERAL => 'Pertanyaan Umum',
                        ContactMessage::SUBJECT_ACADEMICS => 'Akademik & Kurikulum',
                        ContactMessage::SUBJECT_OTHER => 'Lainnya',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Buka Pesan'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('mark_as_read')
                    ->label('Tandai Sudah Dibaca')
                    ->icon('heroicon-o-check')
                    ->action(fn (Collection $records) => $records->each->update(['status' => ContactMessage::STATUS_READ]))
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('mark_as_replied')
                    ->label('Tandai Sudah Dibalas')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(fn (Collection $records) => $records->each->update(['status' => ContactMessage::STATUS_REPLIED]))
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make(),
            ])
            ->paginated([15, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }
}
