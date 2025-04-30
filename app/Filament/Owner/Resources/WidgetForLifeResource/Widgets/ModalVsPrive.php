<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use Filament\Widgets\ChartWidget;

class ModalVsPrive extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
