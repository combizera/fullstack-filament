<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do usuário')
                    ->description(function ($operation) {
                        if ($operation === 'create') {
                            return 'Crie um novo usuário';
                        }
                        return 'Atualize as informações do usuário';
                    })
                    ->columns(2)
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    //->collapsed()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Nome do Usuário')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Email do Usuário')
                            ->unique(ignoreRecord:true)
                            ->required(),

                        TextInput::make('password')
                            ->rules(['required'])
                            ->label('Senha')
                            ->placeholder('******')
                            //->revealable()
                            ->required(),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->mask('(99) 99999-9999')
                            ->placeholder('(__) _____-____')
                            ->required()
                    ]),
                Section::make('Imagem de Perfil')
                    ->description('Avatar do usuário')
                    ->schema([
                        FileUpload::make('avatar')
                            ->directory('avatars')
                            ->imageEditor()
                            ->columnSpanFull()
                            ->image()
                    ]),

                Section::make('Poderes')
                    ->description('Escolha se o novo usuário será administrador')
                    ->schema([
                        Toggle::make('is_admin')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_admin')
                    ->label('Administrador')
                    ->boolean(),
                    //->icon(function (string $state) {
                    //    return $state === '1' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    //})
                    //->color(function (string $state) {
                    //    return $state === '1' ? 'success' : 'danger';
                    //})
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('comments_count')
                    ->label('Comentários')
                    ->sortable()
                    ->counts('comments'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('updated_em')
                    ->label('Atualizado em')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->color('primary')
                        ->label('Editar usuário'),

                    ViewAction::make()
                        ->color('primary')
                        ->icon('heroicon-o-document-text')
                        ->slideOver()
                        ->label('Ver usuário'),

                    Tables\Actions\Action::make('is_admin')
                        ->label(function (User $record){
                            return $record->is_admin ? 'Remover admin' : 'Tornar admin';
                        })
                        ->color(function (User $record){
                            return $record->is_admin ? 'danger' : 'success';
                        })
                        ->action(function (User $record) {
                            //dd($record);
                            $record->is_admin = !$record->is_admin;
                            $record->save();
                        })
                        ->after(function (User $record) {
                            if($record->is_admin){
                                Notification::make()
                                    ->success()
                                    ->duration(2000)
                                    ->title('Usuário é admin')
                                    ->body('Usuário agora é admin')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->duration(2000)
                                    ->title('Usuário não é admin')
                                    ->body('Usuário agora não é admin')
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-user'),

                    DeleteAction::make()
                        ->color('danger'),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            //'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }
}
