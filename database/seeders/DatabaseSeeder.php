<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Book::factory(33)->create()->each(function ($book) {
            $numberRandom = random_int(1, 10);

            Review::factory()->count($numberRandom)
                ->good()
                ->for($book)
                ->create();
        });

        Book::factory(33)->create()->each(function ($book) {
            $numberRandom = random_int(1, 10);

            Review::factory()->count($numberRandom)
                ->average()
                ->for($book)
                ->create();
        });

        Book::factory(34)->create()->each(function ($book) {
            $numberRandom = random_int(1, 10);

            Review::factory()->count($numberRandom)
                ->bad()
                ->for($book)
                ->create();
        });
    }
}