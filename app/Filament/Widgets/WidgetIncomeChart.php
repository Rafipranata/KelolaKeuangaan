<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;


class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static string $color = 'success';
    protected static ?string $heading = 'Pemasukan';

    protected function getData(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
        Carbon::parse($this->filters['startDate']) :
        null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
        Carbon::parse($this->filters['endDate']) :
        now()->endOfMonth();

        $data = Trend::query(Transaction::incomes())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('jumlah');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Perhari',
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
