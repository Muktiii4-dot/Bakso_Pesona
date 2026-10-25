<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the configured administrator if the account does not already exist.
     */
    public function run(): void
    {
        $email = config('bakso_pesona.admin_seed.email');
        $password = config('bakso_pesona.admin_seed.password');

        if (! is_string($email) || trim($email) === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException(
                'Set BAKSO_PESONA_ADMIN_EMAIL and BAKSO_PESONA_ADMIN_PASSWORD before running this seeder.'
            );
        }

        $user = User::firstOrNew(['email' => trim($email)]);

        if ($user->exists) {
            if (! $user->isAdmin()) {
                throw new LogicException('The configured administrator email belongs to a non-admin account.');
            }

            return;
        }

        $user->fill([
            'name' => 'Administrator Bakso Pesona',
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $user->save();
    }
}
