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
            'name' => 'System',
            'username' => 'system',
            'email' => 'system@squadonmission.local',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Joe',
            'username' => 'joe',
            'email' => 'joe@squadonmission.local',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Gowtham',
            'username' => 'gowtham',
            'email' => 'gowtham@squadonmission.local',
            'password' => bcrypt('password'),
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
