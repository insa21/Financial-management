<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    /**
     * Mengambil statistik untuk widget.
     *
     * @return array
     */
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        // Mengatur nilai awal dan akhir dari filter tanggal
        $startDate = isset($this->filters['startDate']) && $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfMonth();

        $endDate = isset($this->filters['endDate']) && $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])
            : now();

        // Menghitung total pemasukan dan pengeluaran
        $pemasukan = Transaction::income()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $pengeluaran = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Menghitung selisih pemasukan dan pengeluaran
        $selisih = $pemasukan - $pengeluaran;

        // Mengembalikan data statistik
        return [
            Stat::make('Total Pemasukan', number_format($pemasukan, 0, ',', '.'))
                ->description('Peningkatan sebesar ' . number_format($pemasukan, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Pengeluaran', number_format($pengeluaran, 0, ',', '.'))
                ->description('Pengeluaran selama periode ini')
                ->color('danger'),

            Stat::make('Selisih', number_format($selisih, 0, ',', '.'))
                ->color($selisih >= 0 ? 'success' : 'danger'),
        ];
    }
}
