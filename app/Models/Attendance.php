<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'athlete_id', 
        'swim_class_id', 
        'attendance_date', 
        'status', 
        'note', 
        'created_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function swimClass(): BelongsTo
    {
        return $this->belongsTo(SwimClass::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}