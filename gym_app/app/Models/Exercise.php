<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Exercise extends Model
{
    use HasFactory;

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    public function day(): BelongsToMany {
        $this->belongsToMany(Day::class, 'day_exercise');
    }
}
