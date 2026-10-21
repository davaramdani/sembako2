<?php

namespace App\Filament\Pages;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Collection;
use Carbon\Carbon; // [PENTING] Pastikan Carbon di-import

class StockMovementsExport
{
    protected Collection $movements;

    public function __construct(Collection $movements)
    {
        $this->movements = $movements;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menambahkan header
        $sheet->setCellValue('A1', 'Product');
        $sheet->setCellValue('B1', 'Type');
        $sheet->setCellValue('C1', 'Quantity');
        $sheet->setCellValue('D1', 'Unit Price');
        $sheet->setCellValue('E1', 'Total Price');
        $sheet->setCellValue('F1', 'Payment Method');
        $sheet->setCellValue('G1', 'Date');

        // Menambahkan data
        $row = 2;
        foreach ($this->movements as $movement) {
            $sheet->setCellValue('A' . $row, $movement->product->name);
            $sheet->setCellValue('B' . $row, $movement->movement_type === 'in' ? 'Masuk' : 'Keluar');
            $sheet->setCellValue('C' . $row, $movement->quantity);
            $sheet->setCellValue('D' . $row, $movement->price_at_time);
            $sheet->setCellValue('E' . $row, $movement->price_at_time * $movement->quantity);
            $sheet->setCellValue('F' . $row, $movement->payment_method ? ucfirst(str_replace('_', ' ', $movement->payment_method)) : '-');
            
            // [SOLUSI] Gunakan Carbon::parse() untuk mengubah string menjadi objek tanggal sebelum memformatnya
            $sheet->setCellValue('G' . $row, Carbon::parse($movement->movement_date)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s'));
            
            $row++;
        }

        // Menyiapkan file untuk diunduh
        $writer = new Xlsx($spreadsheet);
        $fileName = 'analisis-stok_' . now()->format('Ymd_His') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return [
            'file' => $tempFile,
            'name' => $fileName
        ];
    }
}