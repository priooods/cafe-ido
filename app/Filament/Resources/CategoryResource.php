<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\MCategoryTab;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = MCategoryTab::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Category';
    protected static ?string $breadcrumb = "Category";
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
                TextInput::make('title')->label('Nama Category')->placeholder('Masukan Nama Category')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('title')->label('Nama Category'),
            TextColumn::make('m_status_tabs_id')->label('Status Akun')->badge()->getStateUsing(fn($record) => $record->status ? $record->status->title : 'Tidak Ada'),
        ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('aktivated')
                        ->label('Aktifkan')
                        ->action(function ($record) {
                            $record->update([
                                'm_status_tabs_id' => 8,
                            ]);
                        })
                        ->visible(fn($record) =>  $record->m_status_tabs_id === 9)
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Category')
                        ->modalDescription('Apakah anda yakin ingin mengaktifkan Category ?')
                        ->modalSubmitActionLabel('Aktifkan')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    Action::make('unaktivated')
                        ->label('Non Aktifkan')
                        ->action(function ($record) {
                            $record->update([
                                'm_status_tabs_id' => 9,
                            ]);
                        })
                        ->visible(fn($record) => $record->m_status_tabs_id === 8)
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Non Aktifkan Category')
                        ->modalDescription('Apakah anda yakin ingin mengnonaktifkan Category ?')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
