<x-filament::page>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900"></h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- Total Produk -->
            <x-filament::card class="transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a4 4 0 100 8 4 4 0 000-8z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Total Produk</div>
                        <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Product::count() }}</div>
                    </div>
                </div>
            </x-filament::card>

            <!-- Pemasukan Bulan Ini -->
            <x-filament::card class="transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Pendapatan Bulan ini</div>
                        <div class="text-2xl font-bold text-green-600">
                            Rp {{ number_format(
                                \App\Models\StockMovement::where('movement_type', 'out')
                                    ->whereMonth('movement_date', now()->month)
                                    ->whereYear('movement_date', now()->year)
                                    ->sum(\DB::raw('quantity * price_at_time')),
                                0, ',', '.'
                            ) }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <!-- Pengeluaran Bulan Ini -->
            <x-filament::card class="transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Pengeluaran Bulan ini</div>
                        <div class="text-2xl font-bold text-red-600">
                            Rp {{ number_format(
                                \App\Models\StockMovement::where('movement_type', 'in')
                                    ->whereMonth('movement_date', now()->month)
                                    ->whereYear('movement_date', now()->year)
                                    ->sum(\DB::raw('quantity * price_at_time')),
                                0, ',', '.'
                            ) }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <!-- Pemasukan Hari Ini -->
            <x-filament::card class="transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Pendapatan Hari ini</div>
                        <div class="text-2xl font-bold text-green-600">
                            Rp {{ number_format(
                                \App\Models\StockMovement::where('movement_type', 'out')
                                    ->whereDate('movement_date', now()->toDateString())
                                    ->sum(\DB::raw('quantity * price_at_time')),
                                0, ',', '.'
                            ) }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <!-- Pengeluaran Hari Ini -->
            <x-filament::card class="transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Pengeluaran Hari Ini</div>
                        <div class="text-2xl font-bold text-red-600">
                            Rp {{ number_format(
                                \App\Models\StockMovement::where('movement_type', 'in')
                                    ->whereDate('movement_date', now()->toDateString())
                                    ->sum(\DB::raw('quantity * price_at_time')),
                                0, ',', '.'
                            ) }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

        </div>
    </div>
</x-filament::page>