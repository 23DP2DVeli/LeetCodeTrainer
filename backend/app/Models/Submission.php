<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
use HasFactory;
protected $fillable = [
    'user_id',
    'problem_id',
    'problem_slug',
    'status',
    'language',
    'submitted_at',
    'source',
    'external_key',
];


    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }


public function isAccepted(): bool
{
    return $this->status === 'Accepted';
}

}
