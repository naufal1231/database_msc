<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_entry_id',
        'time_result',
        'rank',
        'status',
        'input_by',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(CompetitionEntry::class, 'competition_entry_id');
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
