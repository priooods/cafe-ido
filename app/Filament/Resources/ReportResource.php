<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use App\Models\TProductTab;
use App\Models\TTransactionCheckoutTab;
use App\Models\TTransactionTab;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = TTransactionCheckoutTab::class;
    protected static ?string $navigationLabel = 'Report';
    protected static ?string $breadcrumb = "Report";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->m_user_role_tabs_id == 1 || auth()->user()->m_user_role_tabs_id == 2) return true;
        else return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('order_id')->label('ORDER ID')->required(),
            TextInput::make('bill')->label('Bill')->placeholder('Masukan Nama Customer')->required(),
            TextInput::make('amount_paid')->label('Jumlah Dibayar')->placeholder('Masukan Nama Customer')->required(),
            TextInput::make('amount_change')->label('Kembalian')->required(),
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
            ->query(
                TTransactionCheckoutTab::whereIn('m_status_tabs_id', [4, 5, 6])->orderBy('id', 'desc')
            )
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
            Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
