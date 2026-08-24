<?php

namespace Database\Seeders;

use App\Models\Lead;
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
        // Create demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create 20 demo leads for the user
        Lead::factory(20)->create([
            'created_by' => $user->id,

            // Seed emails and conversations
            $this->call(EmailSeeder::class);
        ]);
    }
}
