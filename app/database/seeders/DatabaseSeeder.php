<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed SELF client
        Client::firstOrCreate(
            ['id' => 1, 'key' => 'SELF'],
            [
                'name' => 'SELF',
                'company' => 'Internal',
                'notes' => 'This is the internal SELF client. Cannot be deleted.',
            ]
        );

        if (app()->environment('local')) {
            $this->call(DevSeeder::class);
        }
    }
}
