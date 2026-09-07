<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTags;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasTags;

    protected $fillable = ['key', 'name', 'description', 'status', 'client_id', 'budget', 'start_date', 'end_date'];

    protected $casts = ['budget' => 'decimal:2', 'start_date' => 'date', 'end_date' => 'date', 'status' => \App\Enums\ProjectStatus::class];

    public function client() { return $this->belongsTo(Client::class); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function entityKeys() { return $this->morphMany(EntityKey::class, 'keyable'); }
    public function getRouteKeyName() { return 'key'; }
}