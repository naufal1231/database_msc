<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'stroke',
        'distance',
        'category',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CompetitionEntry::class, 'competition_event_id');
    }
}
