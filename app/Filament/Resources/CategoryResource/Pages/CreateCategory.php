<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected ?string $heading = 'Tambah Data Category';
    protected static ?string $title = 'Tambah Category';
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
