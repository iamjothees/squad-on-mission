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
            $client = Client::create([
                'name' => 'Dummy Client ' . $i,
                'company' => 'Acme Corp ' . $i,
                'email' => 'client'.$i.'@example.com',
            ]);
            app(\App\Services\EntityKeyService::class)->generateClientKey($client);
            $clients[] = $client;
        }

        // Seed some projects
        $projects = [];
        $statuses = [\App\Enums\ProjectStatus::PLANNING, \App\Enums\ProjectStatus::ACTIVE, \App\Enums\ProjectStatus::COMPLETED];
        foreach ($clients as $client) {
            $p = Project::create([
                'name' => 'Project for ' . $client->name,
                'client_id' => $client->id,
                'status' => $statuses[array_rand($statuses)],
                'budget' => rand(1000, 50000),
                'description' => 'Dummy project description',
            ]);
            app(\App\Services\EntityKeyService::class)->generateProjectKey($p);
            $projects[] = $p;
        }
        
        // Add a SELF project
        $p = Project::create([
            'name' => 'Internal Redesign',
            'client_id' => 1, // SELF
            'status' => \App\Enums\ProjectStatus::ACTIVE,
            'budget' => null,
            'description' => 'Redesigning our own website',
        ]);
            app(\App\Services\EntityKeyService::class)->generateProjectKey($p);
            $projects[] = $p;

        // Seed some tasks
        $taskStatuses = [\App\Enums\TaskStatus::TODO, \App\Enums\TaskStatus::IN_PROGRESS, \App\Enums\TaskStatus::REVIEW, \App\Enums\TaskStatus::DONE];
        foreach ($projects as $project) {
            for ($i = 1; $i <= 3; $i++) {
                $t = Task::create([
                    'title' => 'Task ' . $i . ' for ' . $project->name,
                    'project_id' => $project->id,
                    'status' => $taskStatuses[array_rand($taskStatuses)],
                    'due_date' => rand(0, 1) ? now()->addDays(rand(-5, 10)) : null,
                ]);
                app(\App\Services\EntityKeyService::class)->generateTaskKey($t);
            }
        }
        
        // Seed some timers for the last 30 days
        $user = \App\Models\User::where('username', 'joe')->first() ?? \App\Models\User::first();
        if ($user) {
            $tasks = Task::all();
            
            for ($i = 0; $i < 50; $i++) {
                // Random date in the last 30 days
                $daysAgo = rand(0, 30);
                $date = now()->subDays($daysAgo);
                
                $duration = rand(600, 14400); // 10 mins to 4 hours
                
                $timer = \App\Models\Timer::create([
                    'user_id' => $user->id,
                    'purpose' => 'Working on random stuff ' . $i,
                    'accumulated_seconds' => $duration,
                    'is_running' => false,
                    'last_started_at' => $date->copy()->subSeconds($duration)->timestamp,
                    'completed_at' => $date,
                ]);
                
                $timer->logs()->create([
                    'started_at' => $date->copy()->subSeconds($duration)->timestamp,
                    'stopped_at' => $date->timestamp,
                    'duration_seconds' => $duration,
                ]);
                
                // Attach randomly
                $r = rand(1, 3);
                if ($r === 1 && $tasks->count() > 0) {
                    $task = $tasks->random();
                    $timer->tasks()->attach($task->id);
                } elseif ($r === 2 && count($projects) > 0) {
                    $project = $projects[array_rand($projects)];
                    $timer->projects()->attach($project->id);
                } elseif ($r === 3 && count($clients) > 0) {
                    $client = $clients[array_rand($clients)];
                    $timer->clients()->attach($client->id);
                }
            }
        }
    }
}
