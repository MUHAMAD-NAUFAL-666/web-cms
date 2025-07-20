<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewLayanan extends ViewRecord
{
    protected static string $resource = LayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Layanan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Layanan')
                                    ->size('lg')
                                    ->weight('bold'),
                                    
                                TextEntry::make('urutan')
                                    ->label('Urutan Tampil')
                                    ->badge(),
                            ]),
                            
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Visual & Tampilan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('icon')
                                    ->label('Icon Layanan')
                                    ->size(150),
                                    
                                ColorEntry::make('warna_tema')
                                    ->label('Warna Tema'),
                            ]),
                    ]),
                    
                Section::make('Pengaturan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('is_active')
                                    ->label('Status Aktif')
                                    ->boolean(),
                                    
                                IconEntry::make('is_featured')
                                    ->label('Layanan Unggulan')
                                    ->boolean(),
                                    
                                TextEntry::make('harga_mulai')
                                    ->label('Harga Mulai Dari')
                                    ->money('IDR')
                                    ->placeholder('Tidak ditentukan'),
                            ]),
                    ]),
            ]);
    }
}
