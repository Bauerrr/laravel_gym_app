<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Day extends Model
{
    use HasFactory;

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    public function exercise(): BelongsToMany {
        $this->belongsToMany(Exercise::class, 'day_exercise');
    }
}
