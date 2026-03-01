<?php

namespace App\Console\Commands\Migrate;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WpCustomers extends WpImportCommand
{
    protected $signature   = 'migrate:wp-customers';
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
            ->select('email', 'first_name', 'last_name', 'phone')
            ->get()
            ->groupBy('email');

        $created = 0;
        $skipped = 0;

        foreach ($customers as $email => $entries) {
            $c = $entries->first();
            $email = strtolower(trim($email));

            // Skip the admin account
            if (User::where('email', $email)->exists()) {
                $user = User::where('email', $email)->first();
                $skipped++;
            } else {
                $name = trim($c->first_name . ' ' . $c->last_name) ?: $email;

                $user = User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Hash::make(Str::random(32)),
                ]);
                $created++;
            }

            // Link orders to this user by billing email
            Order::where('billing_email', $email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

        $linked = Order::whereNotNull('user_id')->count();

        $this->printResult('Clients', $created, $skipped);
        $this->info("  ✓ {$linked} commandes rattachées à un compte");
    }
}
