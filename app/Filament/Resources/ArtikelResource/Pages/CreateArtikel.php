<?php

namespace App\Filament\Resources\ArtikelResource\Pages;

use App\Filament\Resources\ArtikelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateArtikel extends CreateRecord
{
    protected static string $resource = ArtikelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Artikel berhasil dibuat!')
            ->body('Artikel Anda telah berhasil disimpan.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto calculate reading time jika tidak diisi
        if (empty($data['reading_time']) && !empty($data['isi'])) {
            $wordCount = str_word_count(strip_tags($data['isi']));
            $data['reading_time'] = max(1, ceil($wordCount / 200)); // 200 words per minute
        }

        return $data;
    }
}
