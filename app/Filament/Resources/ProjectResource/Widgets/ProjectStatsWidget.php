<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $completedProjects = Project::where('status', 'completed')->count();
        $featuredProjects = Project::where('is_featured', true)->count();
        $activeProjects = Project::whereIn('status', ['development', 'testing'])->count();

        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0;

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('Total semua project')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Completed Projects', $completedProjects)
                ->description("{$completionRate}% completion rate")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, 12, $completedProjects]),

            Stat::make('Active Projects', $activeProjects)
                ->description('Development & Testing')
                ->descriptionIcon('heroicon-o-code-bracket')
                ->color('info')
                ->chart([1, 3, 5, 7, 9, 11, $activeProjects]),

            Stat::make('Featured Projects', $featuredProjects)
                ->description('Showcase projects')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning')
                ->chart([1, 2, 1, 3, 2, 4, $featuredProjects]),
        ];
    }
}