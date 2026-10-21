<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use App\Filament\Widgets\MonthlyMovementBarChart;
use App\Filament\Widgets\MovementTypePieChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getActions(): array
    {
        return [
            // [DIUBAH] Menambahkan label baru pada tombol create
            Actions\CreateAction::make()
                ->label('Add New Stock Movement'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            MovementTypePieChart::class,
            MonthlyMovementBarChart::class,
        ];
    }
}