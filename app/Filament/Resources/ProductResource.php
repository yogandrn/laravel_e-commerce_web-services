<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\PicturesRelationManager;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // input name
                TextInput::make('name')
                        ->label('Nama produk')
                        ->string()
                        ->minLength(4)
                        ->maxLength(255)
                        ->regex('/^[a-zA-Z0-9()\-\/%\s]*$/')
                        ->required(),
                        // ->message('Hanya boleh huruf, angka, dan tanda baca ()-/%'),
                        
                // input category id
                Select::make('category_id')
                        ->label('Kategori') 
                        ->options(
                            Category::latest()->pluck('name', 'id')->toArray() 
                        )
                        ->required() 
                        ->placeholder('Pilih Kategori'), 

                // input description
                Textarea::make('description')
                        ->label('Deskripsi produk')
                        ->string()
                        ->minLength(4)
                        ->maxLength(2000)
                        ->required()
                        ->regex('/^[a-zA-Z0-9.,*%()\\-_\/\s]*$/'),
                        // ->message('Hanya boleh huruf, angka, dan beberapa tanda baca! [,], [.], [(], [)], [%], [*]'),

                // input tags
                Textarea::make('tags')
                        ->label('Tag produk (gunakan huruf kecil dan pisahkan dengan tanda koma)')
                        ->string()
                        ->minLength(2)
                        ->maxLength(255)
                        ->required()
                        ->regex('/^([a-zA-Z0-9\s]+(,[a-zA-Z0-9]+)*)?$/'),
                

                // input price
                TextInput::make('price')
                        ->label('Harga produk')
                        ->numeric()
                        ->minValue(100)
                        ->maxValue(1000000000)
                        ->required(),

                // input weight
                TextInput::make('weight')
                        ->label('Berat produk (gram)')
                        ->numeric()
                        ->minValue(100)
                        ->maxValue(100000)
                        ->required(),

                // input stock
                TextInput::make('count_stock')
                        ->label('Jumlah stok produk')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(10000)
                        ->required(),

                // input sold
                // TextInput::make('count_sold')
                //         ->label('Jumlah produk terjual')
                //         ->numeric()
                //         ->minValue(0)
                //         ->maxValue(100000000)
                //         ->readOnly(),
                
                // input thumbnail
                FileUpload::make('thumbnail')
                        ->label('Sampul produk')
                        ->required()
                        ->image()
                        ->directory('uploads/products/thumbnail')
                        ->maxSize(1024),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama produk')
                    ->searchable(),

                ImageColumn::make('thumbnail'),

                TextColumn::make('price')->label('Harga produk')->prefix('Rp '),

                TextColumn::make('count_stock')->label('Jumlah stok'),

                TextColumn::make('count_sold')->label('Jumlah terjual'),

            ])
            ->filters([
                // filter category
                SelectFilter::make('category_id')
                ->label('Kategori')
                ->relationship('category', 'name'),

            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                // ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    public static function getRelations(): array
    {
        return [
            PicturesRelationManager::class,
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
