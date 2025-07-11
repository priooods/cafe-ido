<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\TProductTab;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Order';
    protected ?string $heading = 'Data Order';
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['m_status_tabs_id'] = 7;
        $bill = 0;
        foreach ($data['item'] as $key => $value) {
            $product = TProductTab::find($value['t_product_tabs_id']);
            $bill += $value['count'] * (int)$product->price;
        }
        $data['bill'] = $bill;
        $data['cashier'] = 0;
        $data['order_id'] = 'ORDER-' . uniqid();
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
