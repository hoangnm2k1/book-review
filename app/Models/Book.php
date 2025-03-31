<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    // public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder {
    //     return $query->where('reviews_count', '>=', $minReviews);
    // }
}
