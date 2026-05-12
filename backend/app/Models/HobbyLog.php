<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HobbyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hobby_id',
        'log_date',
        'title',
        'notes',
        'duration_minutes',
        'specific_value',
        'mood_rating',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hobby(): BelongsTo
    {
        return $this->belongsTo(Hobby::class);
    }
}
