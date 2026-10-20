<?php

namespace App\Providers;

<<<<<<< Updated upstream
use Illuminate\Pagination\Paginator;
=======
use App\Models\DispensationRequest;
use App\Policies\DispensationRequestPolicy;
use Illuminate\Support\Facades\Gate;
>>>>>>> Stashed changes
use Illuminate\Support\ServiceProvider;

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
<<<<<<< Updated upstream
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');
=======
        // Daftarkan Policy untuk Dispensasi Atlet
        Gate::policy(DispensationRequest::class, DispensationRequestPolicy::class);
>>>>>>> Stashed changes
    }
}
