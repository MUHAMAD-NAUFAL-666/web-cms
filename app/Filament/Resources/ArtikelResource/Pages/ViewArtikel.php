<?php

namespace App\Filament\Resources\ArtikelResource\Pages;

use App\Filament\Resources\ArtikelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewArtikel extends ViewRecord
{
    protected static string $resource = ArtikelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Artikel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('judul')
                                    ->label('Judul')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->columnSpan(2),

                                TextEntry::make('status')
                                    ->badge()
                                    ->colors([
                                        'danger' => 'draft',
                                        'success' => 'publish',
                                        'warning' => 'archived',
                                    ]),

                                TextEntry::make('user.name')
                                    ->label('Penulis')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('reading_time')
                                    ->label('Waktu Baca')
                                    ->suffix(' menit')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y, H:i'),
                            ]),
                    ]),

                Section::make('Konten')
                    ->schema([
                        ImageEntry::make('gambar')
                            ->label('Gambar Artikel')
                            ->size(400)
                            ->visibility('public'),

                        TextEntry::make('isi')
                            ->label('Isi Artikel')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO & Metadata')
                    ->schema([
                        TextEntry::make('meta_title')
                            ->label('Meta Title')
                            ->placeholder('—'),

                        TextEntry::make('meta_description')
                            ->label('Meta Description')
                            ->placeholder('—'),

                        TextEntry::make('slug')
                            ->label('URL Slug')
                            ->placeholder('—'),
                    ])
                    ->collapsible(),
            ]);
    }
}
