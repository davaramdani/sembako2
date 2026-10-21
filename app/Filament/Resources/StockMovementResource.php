<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use App\Models\Product;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $recordRouteKeyName = 'id_stock_movement';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Produk')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, ?string $state) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('price_at_time', $product->price);
                            }
                        } else {
                            $set('price_at_time', null);
                        }
                    }),

                Radio::make('movement_type')
                    ->label('Tipe Mutasi')
                    ->options(['in' => 'Masuk', 'out' => 'Keluar'])
                    ->required(),

                TextInput::make('quantity')
                    ->label('Kuantitas')
                    ->numeric()
                    ->required(),

                TextInput::make('price_at_time')
                    ->label('Harga Saat Transaksi')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->disabled(),

                Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'QRIS'          => 'QRIS',
                        'bank_transfer' => 'Virtual Account / Transfer Bank',
                        'alfamart'      => 'Alfamart',
                        'indomaret'     => 'Indomaret',
                        'credit_card'   => 'Kartu Kredit',
                        'cash'          => 'Tunai',
                    ])
                    ->nullable()
                    ->reactive(),

                Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3)
                    ->nullable(),

                FileUpload::make('proof_of_payment_path')
                    ->label('Upload Bukti Pembayaran')
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->image()
                    ->nullable()
                    ->hidden(fn (callable $get) => $get('payment_method') !== 'bank_transfer')
                    ->required(fn (callable $get) => $get('payment_method') === 'bank_transfer'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('movement_type')
                    ->label('Tipe')
                    ->enum(['in' => 'Masuk', 'out' => 'Keluar'])
                    ->colors(['success' => 'in', 'danger' => 'out']),

                TextColumn::make('quantity')
                    ->label('Kuantitas'),

                TextColumn::make('price_at_time')
                    ->label('Harga')
                    ->money('idr', true),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'QRIS'          => 'QRIS',
                        'bank_transfer' => 'Virtual Account / Transfer Bank',
                        'alfamart'      => 'Alfamart',
                        'indomaret'     => 'Indomaret',
                        'credit_card'   => 'Kartu Kredit',
                        'cash'          => 'Tunai',
                        default         => $state ? $state : '-',
                    }),

                TextColumn::make('movement_date')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit'   => Pages\EditStockMovement::route('/{record:id_stock_movement}/edit'),
        ];
    }
}