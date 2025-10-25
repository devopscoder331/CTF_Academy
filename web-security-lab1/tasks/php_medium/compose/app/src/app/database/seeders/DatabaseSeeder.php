<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Technical account for testing
        User::create([
            'name' => 'Technical Support',
            'email' => 'tech@environment.htb',
            'password' => bcrypt('TestPass2024'),
            'profile_picture' => 'hish.png',
        ]);
    }
}
