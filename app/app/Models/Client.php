<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['key', 'name', 'email', 'phone', 'company', 'notes'];

    public function projects() { return $this->hasMany(Project::class); }
    public function entityKeys() { return $this->morphMany(EntityKey::class, 'keyable'); }
    
    public function getRouteKeyName() { return 'key'; }
    public function tasks() { return $this->hasManyThrough(Task::class, Project::class); }
    public function getAllTimers() {
        $projectIds = $this->projects()->pluck('id');
        $taskIds = \App\Models\Task::whereIn('project_id', $projectIds)->pluck('id');
        return \App\Models\Timer::whereHas('projects', function($q) use ($projectIds) {
            $q->whereIn('id', $projectIds);
        })->orWhereHas('tasks', function($q) use ($taskIds) {
            $q->whereIn('id', $taskIds);
        })->orWhereHas('clients', function($q) {
            $q->where('id', $this->id); // Include direct client timers just in case
        })->latest('updated_at')->get();
    }

    public function timers() { return $this->morphToMany(Timer::class, 'timerable'); }
}
