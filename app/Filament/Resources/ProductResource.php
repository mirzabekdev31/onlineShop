<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([

Select::make('category_id')
    ->label('Category')
    ->relationship('category', 'name')
    ->searchable()
    ->preload()
    ->required(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->maxLength(1000),

            TextInput::make('price')
                ->numeric()
                ->required(),

            FileUpload::make('image')
                ->image()
                ->directory('products')
                ->required(),
        ]);
}

public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
           TextColumn::make('category.name')->label('Category'),

           ImageColumn::make('image')
                ->label('Rasm')
                ->url(fn ($record) => asset('storage/' . $record->image)) // Ustiga bosilganda ko‘rsatish uchun
                ->getStateUsing(fn ($record) => asset('storage/' . $record->image))
                ->circular()
                ->height(60),

            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('price')->money('usd', true),
            TextColumn::make('created_at')->dateTime('d-m-Y'),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
