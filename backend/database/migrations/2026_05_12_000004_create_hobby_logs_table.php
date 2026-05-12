<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hobby_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hobby_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('specific_value')->nullable();
            $table->unsignedTinyInteger('mood_rating')->nullable();
            $table->timestamps();

            $table->index(['log_date', 'mood_rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hobby_logs');
    }
};
