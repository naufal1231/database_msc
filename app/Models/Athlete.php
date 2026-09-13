<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'photo',
        'current_class_id',
        'join_date',
        'parent_user_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'ku' => 'integer',
    ];

    public function currentClass(): BelongsTo
    {
        return $this->belongsTo(SwimClass::class, 'current_class_id');
    }

    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(ClassHistory::class, 'athlete_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CompetitionEntry::class, 'athlete_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function getMonthlyAttendancePercentage(int $month, int $year): float
    {
        $workDays = $this->calculateWorkDays($month, $year);
        $presentCount = $this->attendances()
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'hadir')
            ->count();
            
        return $workDays > 0 ? ($presentCount / $workDays) * 100 : 0;
    }
    
    private function calculateWorkDays(int $month, int $year): int
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $weekendCount = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $timestamp = mktime(0, 0, 0, $month, $day, $year);
            if (date('N', $timestamp) >= 6) { // 6=Saturday, 7=Sunday
                $weekendCount++;
            }
        }
        
        return $daysInMonth - $weekendCount;
    }
}
