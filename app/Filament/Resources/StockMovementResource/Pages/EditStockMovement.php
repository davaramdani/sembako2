<?php
namespace App\Filament\Resources\StockMovementResource\Pages;
use App\Filament\Resources\StockMovementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
class EditStockMovement extends EditRecord {
    protected static string $resource = StockMovementResource::class;
    protected function getActions(): array { return [ Actions\DeleteAction::make(), ]; }
}