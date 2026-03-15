<?php

namespace App\Console\Commands\Migrate;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class WpCustomers extends WpImportCommand
{
    protected $signature = 'migrate:wp-customers';

    protected $description = 'Crée les comptes clients à partir des commandes WooCommerce (invités)';

    public function handle(): void
    {
        $this->info('Import clients depuis les commandes...');

        // Get unique customers from billing addresses
        $customers = $this->wp()
            ->table('wc_order_addresses')
            ->where('address_type', 'billing')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('email', 'first_name', 'last_name', 'phone', 'address_1', 'address_2', 'city', 'postcode', 'country')
            ->get()
            ->groupBy(fn ($c) => strtolower(trim($c->email)));

        // Get shipping addresses too
        $shippingAddresses = $this->wp()
            ->table('wc_order_addresses')
            ->where('address_type', 'shipping')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('email', 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'postcode', 'country')
            ->get()
            ->keyBy(fn ($s) => strtolower(trim($s->email)));

        $created = 0;
        $updated = 0;

        foreach ($customers as $email => $entries) {
            $c = $entries->first();
            $s = $shippingAddresses->get($email);

            $firstName = trim($c->first_name ?? '');
            $lastName = trim($c->last_name ?? '');
            $name = trim("$firstName $lastName") ?: $email;

            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => $name,
                'phone' => $c->phone ?? null,
                'address_1' => $c->address_1 ?? null,
                'address_2' => $c->address_2 ?? null,
                'city' => $c->city ?? null,
                'postcode' => $c->postcode ?? null,
                'country' => $c->country ?? null,
            ];

            if ($s && ! empty($s->address_1)) {
                $userData += [
                    'shipping_first_name' => trim($s->first_name ?? ''),
                    'shipping_last_name' => trim($s->last_name ?? ''),
                    'shipping_address_1' => $s->address_1 ?? null,
                    'shipping_address_2' => $s->address_2 ?? null,
                    'shipping_city' => $s->city ?? null,
                    'shipping_postcode' => $s->postcode ?? null,
                    'shipping_country' => $s->country ?? null,
                ];
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update($userData);
                $updated++;
            } else {
                $user = User::create($userData + [
                    'email' => $email,
                    'password' => Str::random(32),
                ]);
                $created++;
            }

            // Link orders to this user by billing email
            Order::where('billing_email', $email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

        $linked = Order::whereNotNull('user_id')->count();

        $this->printResult('Clients', $created, $updated);
        $this->info("  ✓ {$linked} commandes rattachées à un compte");
    }
}
