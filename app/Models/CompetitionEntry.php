<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompetitionEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'competition_event_id',
        'status',
        'seed_time',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function competitionEvent(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(Result::class, 'competition_entry_id');
    }
}
