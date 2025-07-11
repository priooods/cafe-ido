<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\MCategoryTab;
use App\Models\TProductTab;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = TProductTab::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Product';
    protected static ?string $breadcrumb = "Product";
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
                TextInput::make('name')->label('Nama Product')->placeholder('Masukan Nama Product')->required(),
                TextInput::make('price')->label('Harga Product')->numeric()->placeholder('Masukan Harga Product')->required(),
                Textarea::make('desc')->label('Detail Product')->placeholder('Masukan Detail Product'),
                Select::make('m_category_tabs_id')
                    ->label('Pilih Category')
                    ->relationship('category', 'title')
                    ->placeholder('Cari Category')
                ->options(MCategoryTab::where('m_status_tabs_id', 10)->pluck('title', 'id'))
                    ->searchable()
                    ->required()
                    ->getSearchResultsUsing(fn(string $search): array => MCategoryTab::where('m_status_tabs_id', 8)->where('title', 'like', "%{$search}%")->limit(5)->pluck('title', 'id')->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => MCategoryTab::find($value)?->title),
                FileUpload::make('path')->label('Upload Foto Product')
                    ->uploadingMessage('Uploading attachment...')
                    ->reorderable()
                    ->preserveFilenames()
                    ->image()
                    ->directory('foto-product')
                    ->maxSize(5000)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Product'),
                TextColumn::make('price')->label('Harga'),
                TextColumn::make('m_category_tabs_id')->label('Category')->badge()->getStateUsing(fn($record) => $record->category ? $record->category->title : 'Tidak Ada'),
                TextColumn::make('desc')->label('Detail'),
                ImageColumn::make('path')->label('Gambar')->getStateUsing(function (TProductTab $record): string {
                            return $record->path;
                        }),
                TextColumn::make('m_status_tabs_id')->label('Status')->badge()
                    ->getStateUsing(fn($record) => $record->status ? $record->status->title : 'Tidak Ada')
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Available' => 'success',
                        'Not Available' => 'danger'
                    })
            
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->visible(fn($record) =>  $record->m_status_tabs_id === 1),
                    Action::make('drafted')
                        ->label('Draft')
                        ->action(function ($record) {
                            $record->update([
                                'm_status_tabs_id' => 1,
                            ]);
                        })
                        ->visible(fn($record) =>  $record->m_status_tabs_id === 2 || $record->m_status_tabs_id === 3)
                        ->icon('heroicon-o-check')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Drafted Product')
                        ->modalDescription('Apakah anda yakin product ini ditarik dari daftar menu ?')
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    Action::make('available')
                        ->label('Tersedia')
                        ->action(function ($record) {
                            $record->update([
                                'm_status_tabs_id' => 2,
                            ]);
                        })
                        ->visible(fn($record) =>  $record->m_status_tabs_id === 1 || $record->m_status_tabs_id === 3)
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Product Tersedia')
                        ->modalDescription('Apakah anda yakin product ini dapat dipesan oleh pelanggan ?')
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    Action::make('notavailable')
                        ->label('Sold Out')
                        ->action(function ($record) {
                            $record->update([
                                'm_status_tabs_id' => 3,
                            ]);
                        })
                        ->visible(fn($record) => $record->m_status_tabs_id === 2)
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Product Sold Out')
                        ->modalDescription('Apakah anda yakin product ini sudah sold out ?')
                        ->modalSubmitActionLabel('Non Aktifkan')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
