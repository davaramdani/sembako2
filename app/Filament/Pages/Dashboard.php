<?php

namespace App\Filament\Pages;
use Filament\Pages\Dashboard as BaseDashboard;

use Filament\Pages\Page;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';              
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $navigationIcon  = 'heroicon-o-home';
}
