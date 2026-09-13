<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'class_id',
        'start_date',
        'end_date',
        'notes',
        'changed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function swimClass(): BelongsTo
    {
        return $this->belongsTo(SwimClass::class, 'class_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
