<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaResource\Pages;
use App\Models\MUserRoleTab;
use App\Models\User;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $breadcrumb = "Pengguna";
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
            TextInput::make('name')->label('Nama Akun')->placeholder('Masukan Nama Akun')->required(),
            TextInput::make('email')->email()->label('Email Akun')->placeholder('Masukan Email Akun')->required(),
            TextInput::make('password')->label('Password Akun')
                ->password()->revealable()
                ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                ->same('passwordConfirmation')
                ->placeholder('Masukan Password')
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->required()
                ->afterStateHydrated(function (TextInput $component, $state) {
                    $component->state('');
                }),
            TextInput::make('passwordConfirmation')->label('Confirmasi Password Akun')->password()->revealable()->placeholder('Masukan Password')->required(),
            Select::make('m_user_role_tabs_id')
                ->label('Pilih Roles')
                ->relationship('roles', 'title')
                ->placeholder('Cari Roles')
                ->options(MUserRoleTab::pluck('title', 'id'))
                ->searchable()
                ->required()
                ->getSearchResultsUsing(fn(string $search): array => MUserRoleTab::where('title', 'like', "%{$search}%")->limit(5)->pluck('title', 'id')->toArray())
                ->getOptionLabelUsing(fn($value): ?string => MUserRoleTab::find($value)?->title),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->query(
                User::whereNot('id', auth()->user()->id)
            )
            ->columns([
            TextColumn::make('name')->label('Nama Pengguna'),
            TextColumn::make('email')->label('Email Pengguna'),
            TextColumn::make('m_user_role_tabs_id')->label('Role')->badge()->getStateUsing(fn($record) => $record->roles ? $record->roles->title : 'Tidak Ada'),
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
                        'm_status_tabs_id' => 10,
                            ]);
                        })
                    ->visible(fn($record) =>  $record->m_status_tabs_id === 11)
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Akun')
                        ->modalDescription('Apakah anda yakin ingin mengaktifkan Akun ?')
                        ->modalSubmitActionLabel('Aktifkan')
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    Action::make('unaktivated')
                        ->label('Non Aktifkan')
                        ->action(function ($record) {
                            $record->update([
                        'm_status_tabs_id' => 11,
                            ]);
                        })
                    ->visible(fn($record) => $record->m_status_tabs_id === 10)
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Non Aktifkan Akun')
                        ->modalDescription('Apakah anda yakin ingin mengnonaktifkan Akun ?')
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
            'index' => Pages\ListPenggunas::route('/'),
            'create' => Pages\CreatePengguna::route('/create'),
            'edit' => Pages\EditPengguna::route('/{record}/edit'),
        ];
    }
}
