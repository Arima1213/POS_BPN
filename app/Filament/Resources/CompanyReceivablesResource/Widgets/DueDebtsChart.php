<?php

namespace App\Filament\Resources\CompanyReceivablesResource\Widgets;

use App\Models\Debt;
use Filament\Widgets\ChartWidget;

class DueDebtsChart extends ChartWidget
{
    protected static ?string $heading = '📥 Utang Jatuh Tempo';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $labels = [];
        $dueCounts = [];

        foreach (range(5, 0) as $i) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $labels[] = $month->format('M Y');

            $dueCounts[] = Debt::whereBetween('due_date', [$start, $end])
                ->whereRaw('amount > paid')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Utang Jatuh Tempo',
                    'data' => $dueCounts,
                    'backgroundColor' => '#f87171',
                    'borderColor' => '#f87171',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}