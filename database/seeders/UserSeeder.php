<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::unguarded(function () {
            User::query()->updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Administrator',
                    'nip' => null,
                    'id_ruang' => null,
                    'kamar' => null,
                    'email_verified_at' => now(),
                    'password' => 'password',
                    'remember_token' => null,
                    'google_id' => null,
                ]
            );
        });
    }
}
