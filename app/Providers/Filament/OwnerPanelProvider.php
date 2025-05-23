<?php

namespace App\Providers\Filament;

use App\Filament\Owner\Resources\IncomeStatementResource\Widgets\LabaRugiTren;
use App\Filament\Owner\Resources\TransactionLogResource\Widgets\TotalBiayaOperasional;
use App\Filament\Owner\Resources\TransactionLogResource\Widgets\TotalPendapatanBulanIni;
use App\Filament\Owner\Resources\TransactionResource\Widgets\TotalPriveBulanIni;
use App\Filament\Owner\Resources\WidgetForLifeResource\Widgets\ModalVsPrive;
use App\Filament\Owner\Resources\WidgetForLifeResource\Widgets\PendapatanBiayaPriveBulanIni;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\Modal;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->brandName('Berkah Padi Nusantara')

            ->id('owner')
            ->spa()
            ->path('owner')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Owner/Resources'), for: 'App\\Filament\\Owner\\Resources')
            ->discoverPages(in: app_path('Filament/Owner/Pages'), for: 'App\\Filament\\Owner\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Owner/Widgets'), for: 'App\\Filament\\Owner\\Widgets')
            ->widgets([
                PendapatanBiayaPriveBulanIni::class,
                LabaRugiTren::class,
                ModalVsPrive::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
