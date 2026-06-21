<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RehashPlaintextPasswords extends Command
{
    protected $signature   = 'users:rehash-passwords';
    protected $description = 'Find users whose password was stored in plaintext (pre-casts fix) and rehash them with bcrypt.';

    public function handle(): int
    {
        // Bcrypt hashes start with $2y$ or $2b$; argon2 with $argon2.
        // Any password not starting with one of these prefixes was stored plaintext.
        $fixed = 0;

        User::query()
            ->where(function ($q) {
                $q->whereRaw("password NOT LIKE '\$2y\$%'")
                  ->whereRaw("password NOT LIKE '\$2b\$%'")
                  ->whereRaw("password NOT LIKE '\$argon2%'");
            })
            ->chunkById(100, function ($users) use (&$fixed) {
                foreach ($users as $user) {
                    // Skip empty / null passwords (social-auth accounts, etc.)
                    if (empty($user->getRawOriginal('password'))) continue;

                    // Bypass the hashed cast — it would double-hash if we called $user->password = ...
                    $user->updateQuietly(['password' => Hash::make($user->getRawOriginal('password'))]);
                    $fixed++;
                }
            });

        $this->info("Done. {$fixed} user password(s) re-hashed.");

        return Command::SUCCESS;
    }
}
