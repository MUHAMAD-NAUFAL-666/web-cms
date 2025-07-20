<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Project;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Tambah Project Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Project')
                ->badge(Project::count())
                ->badgeColor('primary'),
            
            'planning' => Tab::make('Planning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'planning'))
                ->badge(Project::where('status', 'planning')->count())
                ->badgeColor('warning'),
            
            'development' => Tab::make('Development')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'development'))
                ->badge(Project::where('status', 'development')->count())
                ->badgeColor('info'),
            
            'testing' => Tab::make('Testing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'testing'))
                ->badge(Project::where('status', 'testing')->count())
                ->badgeColor('primary'),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(Project::where('status', 'completed')->count())
                ->badgeColor('success'),
            
            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge(Project::where('is_featured', true)->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-star'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectResource\Widgets\ProjectStatsWidget::class,
        ];
    }
}
