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
        return \App\Models\Timer::where(function($q) use ($projectIds, $taskIds) {
            $q->where(function($q1) use ($projectIds) {
                $q1->where('timerable_type', \App\Models\Project::class)->whereIn('timerable_id', $projectIds);
            })->orWhere(function($q2) use ($taskIds) {
                $q2->where('timerable_type', \App\Models\Task::class)->whereIn('timerable_id', $taskIds);
            });
        })->latest('updated_at')->get();
    }
}
