<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'E-commerce';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('shipping_address')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Order & Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('total_price')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'Menunggu Pembayaran' => 'Menunggu Pembayaran',
                                'Diproses'            => 'Diproses',
                                'Dikirim'             => 'Dikirim',
                                'Selesai'             => 'Selesai',
                                'Dibatalkan'          => 'Dibatalkan',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Menunggu',
                                'paid'    => 'Lunas',
                                'failed'  => 'Gagal',
                                'expired' => 'Kadaluwarsa',
                            ])
                            ->required(),

                       Forms\Components\Placeholder::make('payment_method')
    ->label('Metode Pembayaran')
    ->content(fn ($record) => match($record?->payment_method) {
        'QRIS'          => 'QRIS',
        'bank_transfer' => 'Virtual Account / Transfer Bank',
        'alfamart'      => 'Alfamart',
        'indomaret'     => 'Indomaret',
        'credit_card'   => 'Kartu Kredit',
        'cash'          => 'Tunai',
        default         => $record?->payment_method ? $record->payment_method : '-',
    }),

                        Forms\Components\ViewField::make('payment_proof')
                            ->label('Bukti Pembayaran')
                            ->view('filament.components.image-viewer')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('tracking_message')
                            ->label('📦 Pesan & Link Tracking untuk Pelanggan')
                            ->placeholder("Contoh:\nPesanan Anda sedang dalam perjalanan! 🚚\n\nSilakan lacak paket Anda di:\nhttps://cekresi.com/?noresi=JNE123456789ID")
                            ->helperText('Pesan ini akan ditampilkan di halaman pesanan pelanggan.')
                            ->rows(4)
                            ->columnSpanFull()
                            ->hidden(fn (callable $get) => $get('status') !== 'Dikirim'),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pemesan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Menunggu Pembayaran',
                        'primary' => 'Diproses',
                        'info'    => 'Dikirim',
                        'success' => 'Selesai',
                        'danger'  => 'Dibatalkan',
                    ])
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'paid'    => 'Lunas',
                        'pending' => 'Menunggu',
                        'failed'  => 'Gagal',
                        'expired' => 'Kadaluwarsa',
                        default   => ucfirst($state),
                    })
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger'  => fn ($state) => in_array($state, ['failed', 'expired']),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Terbuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}