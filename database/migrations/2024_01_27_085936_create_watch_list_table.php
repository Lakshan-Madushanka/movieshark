<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watch_list', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('imdb_id')->nullable();
            $table->bigInteger('yts_id')->nullable();
            $table->string('image')->nullable();
            $table->string('name');
            $table->json('genres')->nullable();
            $table->date('released_date')->nullable()->index();
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
        Schema::dropIfExists('watch_list');
    }
};
