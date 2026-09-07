<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        // Seed some dummy clients
        $clients = [];
        for ($i = 1; $i <= 5; $i++) {
            $clients[] = Client::create([
                'name' => 'Dummy Client ' . $i,
                'company' => 'Acme Corp ' . $i,
                'email' => 'client'.$i.'@example.com',
            ]);
        }

        // Seed some projects
        $projects = [];
        $statuses = [\App\Enums\ProjectStatus::PLANNING, \App\Enums\ProjectStatus::ACTIVE, \App\Enums\ProjectStatus::COMPLETED];
        foreach ($clients as $client) {
            $projects[] = Project::create([
                'name' => 'Project for ' . $client->name,
                'client_id' => $client->id,
                'status' => $statuses[array_rand($statuses)],
                'budget' => rand(1000, 50000),
                'description' => 'Dummy project description',
            ]);
        }
        
        // Add a SELF project
        $projects[] = Project::create([
            'name' => 'Internal Redesign',
            'client_id' => 1, // SELF
            'status' => \App\Enums\ProjectStatus::ACTIVE,
            'budget' => null,
            'description' => 'Redesigning our own website',
        ]);

        // Seed some tasks
        $taskStatuses = [\App\Enums\TaskStatus::TODO, \App\Enums\TaskStatus::IN_PROGRESS, \App\Enums\TaskStatus::REVIEW, \App\Enums\TaskStatus::DONE];
        foreach ($projects as $project) {
            for ($i = 1; $i <= 3; $i++) {
                Task::create([
                    'title' => 'Task ' . $i . ' for ' . $project->name,
                    'project_id' => $project->id,
                    'status' => $taskStatuses[array_rand($taskStatuses)],
                    'due_date' => rand(0, 1) ? now()->addDays(rand(-5, 10)) : null,
                ]);
            }
        }
    }
}
