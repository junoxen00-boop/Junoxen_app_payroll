<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\MicrosoftGraph\Transport\MicrosoftGraphTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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
    Mail::extend('graph', function () {
        return (new MicrosoftGraphTransportFactory(
    null,
    HttpClient::create()
))->create(
            new Dsn(
                'microsoftgraph+api',
                'default',
                config('services.microsoft_graph.client_id'),
                config('services.microsoft_graph.client_secret'),
                null,
                [
                    'tenantId' => config('services.microsoft_graph.tenant_id'),
                ]
            )
        );
    });

    View::composer('*', function ($view) {
        $view->with(
            'appSetting',
            Setting::first()
        );
    });
}
}