<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organizer',
        'location',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Competition $competition) {
            if ($competition->start_date && $competition->end_date) {
                $competition->attributes['status'] = self::determineStatus(
                    $competition->start_date,
                    $competition->end_date
                );
            }
        });
    }

    /**
     * Tentukan status secara otomatis berdasarkan tanggal pertandingan:
     * - Sebelum tanggal mulai: 'akan_datang'
     * - Sedang berlangsung (start_date <= today <= end_date): 'berlangsung'
     * - Melewati tanggal selesai: 'selesai'
     */
    public static function determineStatus($startDate, $endDate): string
    {
        if (!$startDate || !$endDate) {
            return 'akan_datang';
        }

        $today = now()->format('Y-m-d');
        $start = is_string($startDate) ? substr($startDate, 0, 10) : $startDate->format('Y-m-d');
        $end = is_string($endDate) ? substr($endDate, 0, 10) : $endDate->format('Y-m-d');

        if ($today < $start) {
            return 'akan_datang';
        } elseif ($today > $end) {
            return 'selesai';
        }

        return 'berlangsung';
    }

    /**
     * Accessor agar status selalu dievaluasi dinamis terhadap tanggal hari ini.
     */
    public function getStatusAttribute($value): string
    {
        if ($this->start_date && $this->end_date) {
            return self::determineStatus($this->start_date, $this->end_date);
        }

        return $value ?? 'akan_datang';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            default => 'Akan Datang',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'berlangsung' => 'badge-accent',
            'selesai' => 'badge-success',
            default => 'badge-primary',
        };
    }

    public function getStatusKeteranganAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return '';
        }

        $today = now()->startOfDay();
        $start = $this->start_date->copy()->startOfDay();
        $end = $this->end_date->copy()->endOfDay();

        if ($today->lt($start)) {
            $diff = (int) $today->diffInDays($start);
            return $diff === 0 ? 'Mulai besok' : "Mulai dalam {$diff} hari";
        } elseif ($today->gt($end)) {
            return 'Jadwal pertandingan telah selesai';
        }

        return 'Pertandingan sedang berlangsung';
    }

    public function events(): HasMany
    {
        return $this->hasMany(CompetitionEvent::class, 'competition_id');
    }

    public function dispensationRequests(): HasMany
    {
        return $this->hasMany(DispensationRequest::class);
    }
}
