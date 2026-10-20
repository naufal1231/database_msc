<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'athlete_id',
        'parent_id',
        'school_class',
        'school_absence_no',
        'status',
        'coach_note',
        'verified_by',
        'verified_at',
        'pdf_path',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Orang tua yang mengajukan dispensasi.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Coach/admin yang memverifikasi.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Helper ─────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function hasPdf(): bool
    {
        return !empty($this->pdf_path);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'badge-success',
            'rejected' => 'badge-accent',
            default    => 'badge-primary',
        };
    }
}
