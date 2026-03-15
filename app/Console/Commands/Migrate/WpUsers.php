<?php

namespace App\Console\Commands\Migrate;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WpUsers extends WpImportCommand
{
    protected $signature = 'migrate:wp-users';

    protected $description = 'Importe les utilisateurs/clients depuis WordPress';

    public function handle(): void
    {
        $this->info('Import utilisateurs...');

        $users = $this->wp()->table('users')->get();
        $created = 0;
        $skipped = 0;
        $userMap = [];

        foreach ($users as $u) {
            if (User::where('email', $u->user_email)->exists()) {
                $existing = User::where('email', $u->user_email)->first();
                $userMap[$u->ID] = $existing->id;
                $skipped++;

                continue;
            }

            $meta = $this->wp()
                ->table('usermeta')
                ->where('user_id', $u->ID)
                ->pluck('meta_value', 'meta_key')
                ->all();

            $name = trim(($meta['first_name'] ?? '').' '.($meta['last_name'] ?? ''))
                ?: $u->display_name
                ?: $u->user_login;

            $user = User::create([
                'name' => $name,
                'email' => $u->user_email,
                // Mot de passe temporaire aléatoire — les utilisateurs devront
                // utiliser "Mot de passe oublié" au 1er accès
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'created_at' => $u->user_registered,
            ]);

            $userMap[$u->ID] = $user->id;
            $created++;
        }

        $this->printResult('Utilisateurs', $created, $skipped);
        file_put_contents(storage_path('wp_user_map.json'), json_encode($userMap));
    }
}
