<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('tconst', 10);
            $table->string('title_type', 10);
            $table->string('primary_title', 150);
            $table->unsignedSmallInteger('runtime_minutes');
            $table->string('genres', 50);
            $table->timestamps();

            $table->index(['tconst']);
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
