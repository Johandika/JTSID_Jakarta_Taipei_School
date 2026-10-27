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
                    Section::make(__('admin.sections.sender_information'))
                        ->description(__('admin.descriptions.sender_information'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.full_name'))
                                ->readOnly(),

                            TextInput::make('email')
                                ->label(__('admin.labels.email_address'))
                                ->readOnly(),

                            TextInput::make('phone')
                                ->label(__('admin.labels.phone'))
                                ->readOnly()
                                ->placeholder('—'),

                            Select::make('subject')
                                ->label(__('admin.labels.subject_category'))
                                ->options(ContactMessage::subjectOptions())
                                ->disabled(),
                        ])
                        ->columns(2),

                    Section::make(__('admin.sections.message_content'))
                        ->schema([
                            Textarea::make('message')
                                ->label(__('admin.labels.message_body'))
                                ->rows(7)
                                ->readOnly(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.message_follow_up'))
                        ->description(__('admin.descriptions.message_follow_up'))
                        ->schema([
                            Select::make('status')
                                ->label(__('admin.labels.message_status'))
                                ->options(ContactMessage::statusOptions())
                                ->required(),

                            Textarea::make('admin_notes')
                                ->label(__('admin.labels.admin_notes'))
                                ->placeholder(__('admin.placeholders.admin_notes'))
                                ->rows(4),

                            DateTimePicker::make('created_at')
                                ->label(__('admin.labels.sent_at'))
                                ->readOnly(),

                            TextInput::make('ip_address')
                                ->label(__('admin.fields.ip_address'))
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
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->color(fn (ContactMessage $record): string => $record->getStatusBadgeColor())
                    ->formatStateUsing(fn (ContactMessage $record): string => $record->getStatusLabel()),

                TextColumn::make('name')
                    ->label(__('admin.labels.sender'))
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (ContactMessage $record): ?string => $record->phone),

                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('admin.messages.email_copied')),

                TextColumn::make('subject')
                    ->label(__('admin.labels.subject'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (ContactMessage $record): string => $record->getSubjectLabel()),

                TextColumn::make('message')
                    ->label(__('admin.labels.message_excerpt'))
                    ->limit(45)
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label(__('admin.labels.received_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.labels.message_status'))
                    ->options(ContactMessage::statusOptions()),
                SelectFilter::make('subject')
                    ->label(__('admin.labels.subject_category_filter'))
                    ->options(ContactMessage::subjectOptions()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('admin.actions.open_message')),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('mark_as_read')
                    ->label(__('admin.actions.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->action(fn (Collection $records) => $records->each->update(['status' => ContactMessage::STATUS_READ]))
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('mark_as_replied')
                    ->label(__('admin.actions.mark_as_replied'))
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
