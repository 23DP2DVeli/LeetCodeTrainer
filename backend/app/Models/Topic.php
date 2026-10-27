<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_topics')
            ->withPivot('relevance_weight')
            ->withTimestamps();
    }
}
