<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;

class MonthlyMovementBarChart extends ChartWidget
{
    // [DIUBAH] Judul grafik disesuaikan
    protected static ?string $heading = 'Monthly Income and Expenses';
    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // [DIUBAH] Logika pengambilan data diubah untuk menghitung total nilai (harga * kuantitas)
        $movements = StockMovement::selectRaw('DATE_FORMAT(movement_date, "%Y-%m") as month, movement_type, SUM(quantity * price_at_time) as total_value')
            ->groupBy('month', 'movement_type')
            ->orderBy('month')
            ->get();

        $months = $movements->pluck('month')->unique()->values();

        $inData = $months->map(fn($month) => $movements->where('month', $month)->where('movement_type', 'in')->sum('total_value'));
        $outData = $months->map(fn($month) => $movements->where('month', $month)->where('movement_type', 'out')->sum('total_value'));

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Income (Out)',
                    'data' => $outData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',
                ],
                [
                    'label' => 'Expenses (In)',
                    'data' => $inData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                ],
            ],
        ];
    }
}