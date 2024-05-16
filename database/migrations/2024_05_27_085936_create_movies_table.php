<?php

declare(strict_types=1);

use App\Models\WatchList;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignIdFor(WatchList::class)->constrained()->onDeleteCascade();

            $table->string('imdb_id')->nullable();
            $table->bigInteger('yts_id')->nullable();
            $table->string('image')->nullable();
            $table->string('name')->unique();
            $table->json('genres')->nullable();
            $table->integer('my_rating')->nullable();
            $table->year('released_date')->nullable()->index();
            $table->date('downloaded_status')->nullable()->index();
            $table->date('watched_status')->nullable()->index();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
