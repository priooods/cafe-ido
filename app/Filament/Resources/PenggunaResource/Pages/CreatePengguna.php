<?php

namespace App\Filament\Resources\PenggunaResource\Pages;

use App\Filament\Resources\PenggunaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengguna extends CreateRecord
{
    protected static string $resource = PenggunaResource::class;
    protected ?string $heading = 'Tambah Data Pengguna';
    protected static ?string $title = 'Tambah Pengguna';
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['m_status_tabs_id'] = 11;
        return $data;
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('create');
    }
}
