<?php

namespace App\Providers;

use App\Filament\Resources\AuditCuciTanganMedisResource\Widgets\AuditCuciTanganStats;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL::forceScheme('https');

        Livewire::component(
            'app.filament.resources.audit-cuci-tangan-medis-resource.widgets.audit-cuci-tangan-stats',
            AuditCuciTanganStats::class
        );
        Livewire::component(
            'audit-cuci-tangan-stats',
            AuditCuciTanganStats::class
        );
    }
}
