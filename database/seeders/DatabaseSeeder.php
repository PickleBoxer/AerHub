<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles using the RoleSeeder
        $this->call([
            RoleSeeder::class,
        ]);

        // User::factory(10)->withPersonalTeam()->create();

        // Create a regular user
        User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->assignRole('user');

        // Create admin user and assign the admin role
        User::factory()->withPersonalTeam()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');
    }
}
