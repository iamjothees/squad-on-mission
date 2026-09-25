<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System',
            'username' => 'system',
            'email' => env('SYSTEM_EMAIL', 'connect@squadonmission.local'),
            'password' => null,
            'email_verified_at' => null,
        ]);

        User::create([
            'name' => 'Joe',
            'username' => 'joe',
            'email' => 'iamjothees@gmail.com',
            'password' => null,
            'email_verified_at' => null,
        ]);

        User::create([
            'name' => 'Gowtham',
            'username' => 'gowtham',
            'email' => 'gowthamsubramanian1881@gmail.com',
            'password' => null,
            'email_verified_at' => null,
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
