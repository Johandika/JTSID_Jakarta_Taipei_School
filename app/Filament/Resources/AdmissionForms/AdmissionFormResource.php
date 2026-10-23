<?php

namespace App\Filament\Resources\AdmissionForms;

use App\Filament\Resources\AdmissionForms\Pages\CreateAdmissionForm;
use App\Filament\Resources\AdmissionForms\Pages\EditAdmissionForm;
use App\Filament\Resources\AdmissionForms\Pages\ListAdmissionForms;
use App\Filament\Resources\AdmissionForms\Schemas\AdmissionFormForm;
use App\Filament\Resources\AdmissionForms\Tables\AdmissionFormsTable;
use App\Filament\Resources\CmsResource;
use App\Models\AdmissionForm;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdmissionFormResource extends CmsResource
{
    protected static ?string $model = AdmissionForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Form Pendaftaran';

    protected static ?string $pluralModelLabel = 'Form Pendaftaran';

    protected static ?string $modelLabel = 'Form Pendaftaran';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return static::can('create') && AdmissionForm::nextAvailableSlot() !== null;
    }

    public static function form(Schema $schema): Schema
    {
        return AdmissionFormForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionFormsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmissionForms::route('/'),
            'create' => CreateAdmissionForm::route('/create'),
            'edit' => EditAdmissionForm::route('/{record}/edit'),
        ];
    }
}
