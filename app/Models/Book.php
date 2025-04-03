<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
        // Book::title($title)->get(); thay cho Book::where('title', 'LIKE', '%'.$title.'%')->get(); do có scopeTitle()
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query
            ->withCount([
                // 'reviews' => function (Builder $q) use ($from, $to) {
                //     if ($from && !$to) {
                //         $q->where('created_at', '>=', $from);
                //     } elseif (!$from && $to) {
                //         $q->where('created_at', '<=', $to);
                //     } else {
                //         $q->whereBetween('created_at', [$from, $to]);
                //     }
                // },

                'reviews' => fn(Builder $q) => $$this->dateRangeFilter($q, $from, $to)
            ])
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'ratings')->orderBy('reviews_avg_ratings', 'desc');
    }

    public function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } else {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder {
        return $query->where('reviews_count', '>=', $minReviews);
    }
}