<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pemasukan';
    protected static string $color = 'success';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startDate = isset($this->filters['startDate']) && Carbon::hasFormat($this->filters['startDate'], 'Y-m-d')
            ? Carbon::parse($this->filters['startDate'])
            : now()->subDays(30);

        $endDate = isset($this->filters['endDate']) && Carbon::hasFormat($this->filters['endDate'], 'Y-m-d')
            ? Carbon::parse($this->filters['endDate'])
            : now();

        $data = Trend::query(Transaction::income())
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->sum('amount');

        if ($data->isEmpty()) {
            return [
                'datasets' => [['label' => 'Pemasukan per Hari', 'data' => []]],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan per Hari',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
