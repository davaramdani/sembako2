<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
// Impor yang benar untuk kolom
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class StockMovementsRelationManager extends HasManyRelationManager
{
    // Nama relasi di App\Models\Product::stockMovements()
    protected static string $relationship = 'stockMovements';

    // Kolom mana yang dipakai sebagai judul record
    protected static ?string $recordTitleAttribute = 'movement_date';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('movement_type')
                    ->label('Mutation Type')
                    ->options([
                        'in' => 'In',
                        'out' => 'Out',
                    ])
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),
                TextInput::make('price_at_time')
                    ->label('Price at Transaction')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                DateTimePicker::make('movement_date')
                    ->label('Date')
                    ->default(now())
                    ->timezone('Asia/Jakarta')
                    ->format('d-m-Y H:i:s')
                    ->required(),
                Textarea::make('note')
                    ->label('Note')
                    ->rows(3)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                BadgeColumn::make('movement_type')
                    ->label('Type')
                    ->enum([
                        'in'  => 'Masuk',
                        'out' => 'Keluar',
                    ])
                    ->colors([
                        'success' => 'in',
                        'danger'  => 'out',
                    ]),
                
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),

                TextColumn::make('price_at_time')
                    ->label('Price at Transaction')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('movement_date')
                    ->label('Date')
                    ->dateTime('d M Y, H:i:s')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),

                TextColumn::make('note')
                    ->label('Note')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->note),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}