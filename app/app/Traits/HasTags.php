<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function syncTags(array $tagNames)
    {
        $tagIds = collect($tagNames)->map(function ($name) {
            $name = trim($name);
            if (empty($name)) return null;
            
            $tag = Tag::firstOrCreate(['name' => $name]);
            return $tag->id;
        })->filter()->toArray();

        $this->tags()->sync($tagIds);
    }
}
