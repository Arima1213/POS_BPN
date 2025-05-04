<?php

namespace App\Filament\Resources\CompanyReceivablesResource\Widgets;

use App\Models\Debt;
use Filament\Widgets\ChartWidget;

class DebtTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Piutang Tertagih vs Belum Tertagih';
    protected static ?int $sort = 1;
    protected static string $color = 'success';

    protected function getData(): array
    {
        $labels = [];
        $paidData = [];
        $unpaidData = [];

        foreach (range(5, 0) as $i) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $labels[] = $month->format('M Y');

            $debts = Debt::whereBetween('created_at', [$start, $end])->get();

            $paidData[] = $debts->sum('paid');
            $unpaidData[] = $debts->sum(fn($debt) => $debt->amount - $debt->paid);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tertagih',
                    'data' => $paidData,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Belum Tertagih',
                    'data' => $unpaidData,
                    'borderColor' => '#facc15',
                    'backgroundColor' => '#facc15',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
