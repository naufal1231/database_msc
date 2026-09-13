<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'channel',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    /**
     * Get the user recipient.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
