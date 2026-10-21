<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MovementTypePieChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Income and Expenses';
    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

        $movements = StockMovement::query()
            ->whereBetween('movement_date', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(movement_date) as date, movement_type, SUM(quantity * price_at_time) as total_value')
            ->groupBy('date', 'movement_type')
            ->orderBy('date')
            ->get();

        $labels = collect(range(0, 6))->map(fn($day) => $startOfWeek->clone()->addDays($day)->format('l'));

        // [DIPERBAIKI] Menambahkan `$labels` ke dalam `use()`
        $inData = $labels->map(function ($day) use ($movements, $startOfWeek, $labels) {
            $date = $startOfWeek->clone()->addDays(array_search($day, $labels->all()))->format('Y-m-d');
            return $movements->where('date', $date)->where('movement_type', 'in')->sum('total_value');
        });

        // [DIPERBAIKI] Menambahkan `$labels` ke dalam `use()`
        $outData = $labels->map(function ($day) use ($movements, $startOfWeek, $labels) {
            $date = $startOfWeek->clone()->addDays(array_search($day, $labels->all()))->format('Y-m-d');
            return $movements->where('date', $date)->where('movement_type', 'out')->sum('total_value');
        });

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Income (Out)',
                    'data' => $outData,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
                [
                    'label' => 'Expenses (In)',
                    'data' => $inData,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                ],
            ],
        ];
    }
}