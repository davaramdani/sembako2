<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $recordRouteKeyName = 'id_product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->schema([
                        TextInput::make('product_code')->label('Product Code')->disabled()->visibleOn('edit')->afterStateHydrated(function (TextInput $component, $record) { if ($record) { $component->state('PROD-' . str_pad($record->id_product, 5, '0', STR_PAD_LEFT)); } }),
                        FileUpload::make('photo_path')->label('Product Photo')->image()->disk('public')->directory('product-photos')->required(),
                        TextInput::make('name')->label('Product Name')->required()->maxLength(255)->unique(ignoreRecord: true),
                        TextInput::make('price')->label('Price')->numeric()->required()->prefix('Rp'),
                        TextInput::make('current_stock')->label('Current Stock'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_code')->label('Kode Produk')->getStateUsing(fn ($record) => 'PROD-' . str_pad($record->id_product, 5, '0', STR_PAD_LEFT))->searchable(query: function ($query, $search) { $search = ltrim(str_ireplace('PROD-', '', $search), '0'); if (is_numeric($search)) { $query->where('id_product', $search); } }),
                ImageColumn::make('photo_path')->label('Foto Produk')->disk('public'),
                TextColumn::make('name')->label('Nama Produk')->sortable()->searchable(),
                TextColumn::make('price')->label('Harga')->money('idr', true),
                TextColumn::make('current_stock')->label('Sisa Stok'),
                TextColumn::make('created_at')->label('Tanggal Terbuat')->dateTime('d-m-Y H:i:s')->timezone('Asia/Jakarta'),
            ])
            ->filters([
                //
            ])
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
            RelationManagers\StockMovementsRelationManager::class,
        ];
    }

    // [BAGIAN PALING PENTING] Pastikan method ini sama persis
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record:id_product}'),
        ];
    }
}