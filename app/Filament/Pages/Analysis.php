<?php

namespace App\Filament\Pages;

use PDF;
use App\Models\StockMovement;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Pages\Actions\Action;
use App\Filament\Pages\StockMovementsExport;
use Illuminate\Support\Carbon;

class Analysis extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.monitoring-stok';
    protected static ?string $slug = 'monitoring-stok';
    protected static ?string $navigationLabel = 'Stock Monitoring';
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public function getActions(): array { return [ Action::make('exportPdf')->label('Cetak PDF')->icon('heroicon-o-printer')->action('exportPdf')->color('primary')->disabled(fn () => $this->getFilteredTableQuery()->count() === 0), Action::make('exportExcel')->label('Cetak Excel')->icon('heroicon-o-document-download')->action('exportExcel')->color('success')->disabled(fn () => $this->getFilteredTableQuery()->count() === 0), ]; }
    public function exportPdf() { $movements = $this->getFilteredTableQuery()->get(); $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.analysis-pdf', compact('movements')); return response()->streamDownload(fn () => print($pdf->stream()), 'analisis-stok-' . now()->format('Ymd_His') . '.pdf'); }
    public function exportExcel() { $movements = $this->getFilteredTableQuery()->get(); $export = new StockMovementsExport($movements); $result = $export->export(); return response()->download($result['file'], $result['name'])->deleteFileAfterSend(true); }
    protected function getTableQuery() { return StockMovement::with('product'); }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('product.name')->label('Produk')->sortable()->searchable(),
            \Filament\Tables\Columns\BadgeColumn::make('movement_type')->label('Tipe')->enum(['in' => 'Masuk', 'out' => 'Keluar'])->colors(['success' => 'out', 'danger' => 'in']),
            \Filament\Tables\Columns\TextColumn::make('quantity')->label('Kuantitas'),
            \Filament\Tables\Columns\TextColumn::make('price_at_time')->label('Harga Satuan')->money('idr', true),
            \Filament\Tables\Columns\TextColumn::make('total_price')->label('Total Harga')->getStateUsing(fn ($record) => $record->price_at_time * $record->quantity)->money('idr', true),
            \Filament\Tables\Columns\TextColumn::make('payment_method')->label('Metode Pembayaran')->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '-'),
            \Filament\Tables\Columns\TextColumn::make('movement_date')->label('Tanggal')->dateTime('d-m-Y H:i:s')->timezone('Asia/Jakarta'),
        ];
    }

    protected function getTableFilters(): array { return [ \Filament\Tables\Filters\Filter::make('movement_date')->form([\Filament\Forms\Components\DatePicker::make('start_date')->label('Start'), \Filament\Forms\Components\DatePicker::make('end_date')->label('End'),])->query(function ($query, array $data) { return $query ->when($data['start_date'], fn($q) => $q->whereDate('movement_date', '>=', $data['start_date'])) ->when($data['end_date'], fn($q) => $q->whereDate('movement_date', '<=', $data['end_date'])); }), \Filament\Tables\Filters\SelectFilter::make('payment_method')->label('Payment Method')->options(['cash' => 'cash', 'bank_transfer' => 'Bank Transfer'])->query(fn ($query, array $data) => $query->when($data['value'], fn($q) => $query->where('payment_method', $data['value']))), ]; }

    protected function getFilteredQueryForCharts() {
        $query = $this->getFilteredTableQuery();
        if (property_exists($this, 'tableFilters')) {
            $filters = $this->tableFilters;
            $isDateFilterActive = !empty($filters['movement_date']['start_date']) || !empty($filters['movement_date']['end_date']);
            if (!$isDateFilterActive) { $query->whereDate('movement_date', today()); }
        } else { $query->whereDate('movement_date', today()); }
        return $query;
    }

    public function getStatistics() {
        $query = $this->getFilteredQueryForCharts();
        $totalIn = $query->clone()->where('movement_type', 'in')->sum(\DB::raw('quantity * price_at_time'));
        $totalOut = $query->clone()->where('movement_type', 'out')->sum(\DB::raw('quantity * price_at_time'));
        $profit = $totalOut - $totalIn;
        return ['total_in' => number_format($totalIn, 0, ',', '.'), 'total_out' => number_format($totalOut, 0, ',', '.'), 'profit' => number_format($profit, 0, ',', '.'),];
    }

    // [DIHAPUS] Fungsi getChartData, getThisWeekChartData, dan getMonthlyChartData telah dihapus.
}