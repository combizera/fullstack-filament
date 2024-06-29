<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
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
                    ->required(),

                FileUpload::make('avatar')
                    ->directory('avatars')
                    ->imageEditor()
                    ->avatar(),

                Toggle::make('is_admin'),

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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('primary')
                        ->label('Editar usuário'),
                    Tables\Actions\DeleteAction::make()->color('danger'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
