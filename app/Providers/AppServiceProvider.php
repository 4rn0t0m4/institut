<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Observers\CacheClearObserver;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Stripe\ApiRequestor;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;

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
        // OVH mutualisé bloque l'extension PHP curl sur le port 443.
        // On force Guzzle (Laravel Http) à utiliser les stream wrappers PHP natifs.
        Http::globalOptions([
            'handler' => HandlerStack::create(new StreamHandler()),
        ]);

        // Stripe : utiliser un client HTTP basé sur les streams au lieu de curl.
        ApiRequestor::setHttpClient(new \App\Http\Client\StripeStreamClient());

        Mail::extend('brevo', function (array $config) {
            $httpClient = new \Symfony\Component\HttpClient\NativeHttpClient();

            return new BrevoApiTransport($config['key'], $httpClient);
        });

        Product::observe(CacheClearObserver::class);
        Page::observe(CacheClearObserver::class);
        Setting::observe(CacheClearObserver::class);

        $this->loadShippingSettings();
    }

    private function loadShippingSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = Cache::remember('shipping_settings', 3600, function () {
            return Setting::where('group', 'shipping')->pluck('value', 'key')->toArray();
        });

        if (empty($settings)) {
            return;
        }

        if (isset($settings['shipping_colissimo_price'])) {
            config(['shipping.methods.colissimo.price' => (float) $settings['shipping_colissimo_price']]);
        }
        if (isset($settings['shipping_boxtal_price'])) {
            config(['shipping.methods.boxtal.price' => (float) $settings['shipping_boxtal_price']]);
        }
        if (isset($settings['shipping_boxtal_price_international'])) {
            config(['shipping.methods.boxtal.price_international' => (float) $settings['shipping_boxtal_price_international']]);
        }
        if (isset($settings['shipping_free_threshold_fr'])) {
            config([
                'shipping.zones.FR.free_shipping_threshold' => (float) $settings['shipping_free_threshold_fr'],
                'shipping.free_shipping_threshold' => (float) $settings['shipping_free_threshold_fr'],
            ]);
        }
        if (isset($settings['shipping_free_threshold_international'])) {
            config(['shipping.zones.international.free_shipping_threshold' => (float) $settings['shipping_free_threshold_international']]);
        }
    }
}
