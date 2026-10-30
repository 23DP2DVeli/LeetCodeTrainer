<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Problem extends Model
{

use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'difficulty',
        'acceptance_rate',
        'description',
        'constraints',
        'hints',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'hints' => 'array',
            'metadata' => 'array',
            'acceptance_rate' => 'decimal:2',
        ];
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'problem_topics')
            ->withPivot('relevance_weight')
            ->withTimestamps();
    }

    public function submissions(): HasMany
{
    return $this->hasMany(Submission::class);
}
}
