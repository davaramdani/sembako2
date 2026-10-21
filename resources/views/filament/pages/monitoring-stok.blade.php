<x-filament::page>
    <div class="space-y-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @php
                $stats = $this->getStatistics();
            @endphp
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500">Pendapatan Hari ini (Keluar)</div>
                <div class="text-2xl font-bold text-white-600">Rp {{ $stats['total_out'] }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500">Pengeluaran Hari ini (Masuk)</div>
                <div class="text-2xl font-bold text-white-600">Rp {{ $stats['total_in'] }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500">Keuntungan hari ini</div>
                <div class="text-2xl font-bold text-white-600">Rp {{ $stats['profit'] }}</div>
            </x-filament::card>
        </div>

        {{-- Stock Movements Table --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament::page>