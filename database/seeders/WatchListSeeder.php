<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WatchList;
use Illuminate\Database\Seeder;

class WatchListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WatchList::factory()->count(25)->create();
    }
}
