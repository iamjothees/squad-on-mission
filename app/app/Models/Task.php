<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTags;

class Task extends Model
{
    use HasFactory, SoftDeletes, HasTags;

    protected $fillable = ['key', 'title', 'description', 'status', 'project_id', 'priority', 'due_date'];

    protected $casts = ['due_date' => 'datetime', 'status' => \App\Enums\TaskStatus::class, 'priority' => \App\Enums\TaskPriority::class];

    public function project() { return $this->belongsTo(Project::class); }
    public function entityKeys() { return $this->morphMany(EntityKey::class, 'keyable'); }
        public function timers() { return $this->morphToMany(Timer::class, 'timerable'); }
    
    public function getAllTimers() {
        return $this->timers()->latest('updated_at')->get();
    }
    public function getRouteKeyName() { return 'key'; }
}