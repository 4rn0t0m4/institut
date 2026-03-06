<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
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
        Mail::extend('brevo', function (array $config) {
            return new BrevoApiTransport($config['key']);
        });

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
