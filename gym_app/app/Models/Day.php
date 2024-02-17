<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    public function exercises(): BelongsToMany {
        return $this->belongsToMany(Exercise::class, 'day_exercise');
    }

    public function scores(): HasMany {
        return $this->hasMany(Score::class, 'day_score');
    }
}
