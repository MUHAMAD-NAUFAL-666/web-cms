<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('visit')
                ->label('Visit Project')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(fn ($record) => $record->link)
                ->openUrlInNewTab()
                ->visible(fn ($record) => !empty($record->link)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Project Overview')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Project')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                
                                TextEntry::make('klien')
                                    ->label('Klien')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-office'),
                            ]),
                        
                        ImageEntry::make('gambar')
                            ->label('Project Image')
                            ->height(300)
                            ->width('100%')
                            ->extraAttributes(['class' => 'rounded-lg shadow-lg']),
                        
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Section::make('Technical Details')
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('stack')
                                    ->label('Teknologi/Stack')
                                    ->badge()
                                    ->separator(',')
                                    ->color('info'),
                                
                                TextEntry::make('link')
                                    ->label('Project Link')
                                    ->url(fn ($record) => $record->link)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-link')
                                    ->color('primary')
                                    ->placeholder('No link available'),
                            ]),
                    ]),

                Section::make('Project Status & Timeline')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'planning' => 'warning',
                                        'development' => 'info',
                                        'testing' => 'primary',
                                        'completed' => 'success',
                                        'maintenance' => 'gray',
                                        default => 'gray',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'planning' => 'heroicon-o-clock',
                                        'development' => 'heroicon-o-code-bracket',
                                        'testing' => 'heroicon-o-bug-ant',
                                        'completed' => 'heroicon-o-check-circle',
                                        'maintenance' => 'heroicon-o-wrench-screwdriver',
                                        default => 'heroicon-o-question-mark-circle',
                                    }),
                                
                                TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar'),
                                
                                TextEntry::make('tanggal_selesai')
                                    ->label('Target Selesai')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar-days')
                                    ->placeholder('Belum ditentukan'),
                            ]),
                        
                        IconEntry::make('is_featured')
                            ->label('Featured Project')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ]),

                Section::make('Metadata')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-plus-circle'),
                                
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diupdate')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-pencil-square'),
                            ]),
                    ]),
            ]);
    }
}