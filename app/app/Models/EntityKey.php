<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityKey extends Model
{
    protected $fillable = ['key', 'keyable_type', 'keyable_id', 'is_primary'];

    public function keyable()
    {
        return $this->morphTo();
    }
}
