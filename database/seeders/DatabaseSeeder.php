<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Admin dibuat hanya jika kredensial resmi sudah dikonfigurasi di .env.
        // Tidak membuat password default yang berisiko dipakai di produksi.
        if (filled(config('bakso_pesona.admin_seed.email'))
            && filled(config('bakso_pesona.admin_seed.password'))) {
            $this->call(AdminUserSeeder::class);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
