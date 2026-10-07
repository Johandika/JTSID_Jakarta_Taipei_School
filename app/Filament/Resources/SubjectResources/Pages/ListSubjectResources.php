<?php

namespace App\Filament\Resources\SubjectResources\Pages;

use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubjectResources extends ListRecords
{
    protected static string $resource = SubjectResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        return SubjectResourceType::query()
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (SubjectResourceType $type): array => [
                (string) $type->getKey() => Tab::make($type->name)
                    ->icon($this->iconForType($type->code))
                    ->badge(SubjectResource::where('type_id', $type->getKey())->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('type_id', $type->getKey())),
            ])
            ->all();
    }

    private function iconForType(string $code): string
    {
        return match ($code) {
            'calendar' => 'heroicon-o-calendar-days',
            'classroom_regulations' => 'heroicon-o-clipboard-document-list',
            'syllabus' => 'heroicon-o-document-text',
            'textbook' => 'heroicon-o-book-open',
            'lesson_progress' => 'heroicon-o-chart-bar',
            'weekly_news' => 'heroicon-o-newspaper',
            default => 'heroicon-o-folder-open',
        };
    }
}
