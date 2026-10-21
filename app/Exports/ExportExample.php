<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class ExportExample implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['Nama', 'Harga', 'Jumlah'],
            ['Beras', 15000, 10],
            ['Minyak', 20000, 5],
        ]);
    }
}