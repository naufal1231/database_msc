<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SwimClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
    ];

    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class, 'current_class_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ClassHistory::class, 'class_id');
    }
}
