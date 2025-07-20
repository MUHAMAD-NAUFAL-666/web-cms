<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLayanan extends CreateRecord
{
    protected static string $resource = LayananResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Layanan berhasil dibuat!')
            ->body('Layanan baru telah ditambahkan ke sistem.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate urutan jika tidak diisi
        if (!isset($data['urutan']) || $data['urutan'] === 0) {
            $maxUrutan = $this->getModel()::max('urutan') ?? 0;
            $data['urutan'] = $maxUrutan + 1;
        }

        return $data;
    }
}
