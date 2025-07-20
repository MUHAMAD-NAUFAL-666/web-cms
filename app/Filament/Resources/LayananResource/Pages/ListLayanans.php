<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLayanans extends ListRecords
{
    protected static string $resource = LayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Layanan Baru')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Layanan')
                ->badge(fn () => $this->getModel()::count()),
                
            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => $this->getModel()::where('is_active', true)->count())
                ->badgeColor('success'),
                
            'tidak_aktif' => Tab::make('Tidak Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(fn () => $this->getModel()::where('is_active', false)->count())
                ->badgeColor('danger'),
                
            'unggulan' => Tab::make('Unggulan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge(fn () => $this->getModel()::where('is_featured', true)->count())
                ->badgeColor('warning'),
        ];
    }
}
