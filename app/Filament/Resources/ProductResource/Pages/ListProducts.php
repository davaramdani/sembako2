<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions; // Menggunakan namespace yang benar untuk v2
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;


    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add New Product'),
        ];
    }
}