<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Post')
                    ->description('Título e slug')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->helperText('Título do Post')
                            ->maxLength(255)
                            ->required()
                            ->live(debounce:500)
                            ->afterStateUpdated(function ($state, $set){
                                $set('slug', Str::slug($state));
                            })
                            ->placeholder('Insira aqui o título da postagem'),
                        TextInput::make('slug')
                            ->helperText('slug do Post')
                            ->maxLength(255)
                            ->required()
                            ->placeholder('Insira aqui o slug da postagem'),
                    ]),

                Section::make('Conteúdo do Post')
                    ->schema([
                        RichEditor::make('content')
                            ->helperText('Conteúdo do Post')
                            ->placeholder('Insira aqui o conteúdo da postagem')
                            ->required(),
                    ]),

                Section::make('Thumbnail da postagem')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->placeholder('Insira aqui a thumb')
                            ->directory('thumbs')
                            ->required(),
                    ]),

                Section::make('Categorias e Tags')
                    ->description('Defina aqui as categorias e tags do post')
                    ->columns(2)
                    ->schema([
                        Select::make('category_id')
                            ->label('Categoria')
                            ->searchable()
                            //outra opção de busca das categorias
                            //->relationship('category', 'name')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->preload(),

                        Select::make('tags')
                            ->label('Tags')
                            ->searchable()
                            ->relationship('tags', 'tag_name')
                            ->multiple()
                            ->preload(),
                    ]),

                Section::make('Publish')
                    ->schema([
                        Select::make('is_published')
                            ->label('Publicado?')
                            ->options([
                                0 => 'Rascunho',
                                1 => 'Publicado'
                            ])
                        ->default('Rascunho')
                        ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\IconColumn::make('is_published')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
