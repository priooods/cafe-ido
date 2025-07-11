<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\TProductTab;
use App\Models\TTransactionCheckoutTab;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = TTransactionCheckoutTab::class;
    protected static ?string $navigationLabel = 'Order';
    protected static ?string $breadcrumb = "Order";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('customer_name')->label('Nama Customer')->placeholder('Masukan Nama Customer')->required(),
            TextInput::make('customer_phone')->label('Nomor Customer')->numeric()->placeholder('Masukan Nomor Customer'),
            Textarea::make('notes')->label('Notes Pesanan')->placeholder('Masukan Notes Pesanan'),
            TextInput::make('table_number')->label('Nomor Meja')->placeholder('Masukan Nomor Meja')->required(),
            Section::make('Informasi Product')->schema([
                Repeater::make('item')
                    ->schema([
                        Grid::make([
                            'default' => 2,
                        ])->schema([
                            Select::make('t_product_tabs_id')
                                ->label('Pilih Product')
                                ->relationship('product', 'name')
                                ->placeholder('Cari Product')
                                ->options(TProductTab::where('m_status_tabs_id', 2)->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->getSearchResultsUsing(fn(string $search): array => TProductTab::where('m_status_tabs_id', 2)->where('name', 'like', "%{$search}%")->limit(5)->pluck('name', 'id')->toArray())
                                ->getOptionLabelUsing(fn($value): ?string => TProductTab::find($value)?->name),
                            TextInput::make('count')->label('Jumlah Product')->numeric()->placeholder('Masukan Jumlah Product'),
                        ])
                    ])
                    ->label('Lengkapi Pesanan')
                    ->relationship()
                    ->id('t_transaction_checkout_tabs_id')
                    ->defaultItems(1)
                    ->reorderable(true)
                    ->dehydrated(true)
                    ->reorderableWithButtons()
                    ->reorderableWithDragAndDrop(true)
                    ->collapsible()
                    ->addActionLabel('Tambah Product'),
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('order_id')->label('ORDER ID'),
            TextColumn::make('table_number')->label('Meja')->alignment(Alignment::Center),
            TextColumn::make('customer_name')->label('Customer'),
            TextColumn::make('bill')->label('BILL'),
            TextColumn::make('amount_paid')->label('PAID'),
            TextColumn::make('m_status_tabs_id')->label('Status')->badge()->alignment(Alignment::Center)
                ->getStateUsing(fn($record) => $record->status ? $record->status->title : 'Tidak Ada')
                ->color(fn(string $state): string => match ($state) {
                    'Waiting Payment' => 'gray',
                    'Complete Payment' => 'success',
                    'Failure Payment' => 'danger',
                    'Refund Payment' => 'warning',
                    'Waiting Order' => 'info',
                    'Complete Order' => 'success',
                }),
        ])
            ->filters([
                //
            ])
            ->actions([
            ActionGroup::make([
                Action::make('complete')
                    ->label('Payment Complete')
                    ->action(function (array $data, TTransactionCheckoutTab $record) {
                        $record->update([
                            'm_status_tabs_id' => 6,
                            'amount_paid' => $data['amount_paid'],
                        ]);
                    })
                    ->form([
                        TextInput::make('amount_paid')->numeric()->label('Total Dibayar')->placeholder('Masukan Total Dibayar')->required(),
                    ])
                    ->visible(fn($record) => $record->m_status_tabs_id === 7)
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Selesai')
                    ->modalDescription('Apakah Pembayaran pesanan ini sudah sesuai dengan bill ?')
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                Action::make('waiting_order')
                    ->label('Waiting Order')
                    ->action(function ($record) {
                        $record->update([
                            'm_status_tabs_id' => 4,
                        ]);
                    })
                    ->visible(fn($record) => $record->m_status_tabs_id === 6)
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Waiting Order')
                    ->modalDescription('Apakah Pesanan ini sedang dibuat ?')
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                Action::make('complete_order')
                    ->label('Complete Order')
                    ->action(function ($record) {
                        $record->update([
                            'm_status_tabs_id' => 5,
                        ]);
                        })
                        ->visible(fn($record) => $record->m_status_tabs_id === 4)
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pesanan Selesai')
                        ->modalDescription('Apakah anda yakin Pesanan ini telah selesai ?')
                        ->modalSubmitActionLabel('Konfirmasi')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    Tables\Actions\ViewAction::make()->modalHeading('Detail Pesanan')
                ])->button()->label('Aksi')->color('info')
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
