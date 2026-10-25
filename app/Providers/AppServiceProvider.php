<?php

namespace App\Providers;

use App\Services\UlangTahunService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Data lonceng notifikasi, otomatis tersedia di layout utama (layouts/app.blade.php)
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();

            if (! $user) {
                return;
            }

            $service = app(UlangTahunService::class);
            $ulangTahun = $service->mendatang($user, 5);
            $service->catat($user, $ulangTahun);

            $view->with([
                'notifUlangTahun' => $ulangTahun,
                'notifTerbaru' => $user->activityLogs()->latest()->orderByDesc('id')->limit(5)->get(),
                'notifBelumDibaca' => $user->activityLogs()->whereNull('dibaca_pada')->count(),
            ]);
        });
    }
}
