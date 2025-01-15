<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pemasukan = Transaction::income()->get()->sum('amount');
        $pengeluaran = Transaction::expenses()->get()->sum('amount');

        return [
            Stat::make('Total Pemasukan', $pemasukan)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-cash')
                ->color('success')
                ->icon('heroicon-m-cash'),
            Stat::make('Total Pengeluaran', $pengeluaran),
            Stat::make('Selisih', $pemasukan - $pengeluaran),
        ];
    }
}
