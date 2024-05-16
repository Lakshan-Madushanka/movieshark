<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WatchList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = ['crime', 'drama', 'adventure', 'thriller', 'mystery'];
        $genresCount = count($genres);

        return [
            'watch_list_id' => WatchList::factory(),
            'imdb_id' => $this->faker->word(),
            'yts_id' => $this->faker->randomDigit(),
            'image' => $this->faker->imageUrl(),
            'name' => $this->faker->unique()->word(),
            'genres' => $this->faker->randomElements($genres, random_int(1, $genresCount)),
            'my_rating' => $this->faker->numberBetween(0, 100),
            'released_date' => $this->faker->randomElement([null, $this->faker->date()]),
            'downloaded_status' => $this->faker->randomElement([null, $this->faker->date]),
            'watched_status' => $this->faker->randomElement([null, $this->faker->date]),
            'description' => $this->faker->words(5, true),
        ];
    }
}
