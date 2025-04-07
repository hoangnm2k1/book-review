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

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to),
        ]);
    }

    public function scopeWithAvgRating(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_ratings', 'desc');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withReviewsCount()->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvgRating()->orderBy('reviews_avg_rating', 'desc');
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

    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        return $query->where('reviews_count', '>=', $minReviews);
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query
            ->popular(now()->subMonth(), now())
            ->hightestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query
            ->popular(now()->subMonth(6), now())
            ->hightestRated(now()->subMonth(6), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query
            ->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        return $query
            ->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

    protected static function booted()
    {
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
    }
}
