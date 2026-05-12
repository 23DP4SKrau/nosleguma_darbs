<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hobbies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->enum('difficulty', ['viegls', 'videjs', 'sarezgits'])->default('viegls');
            $table->enum('estimated_cost', ['bezmaksas', 'zemas', 'videjas', 'augstas'])->default('bezmaksas');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['name', 'difficulty', 'estimated_cost']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hobbies');
    }
};
