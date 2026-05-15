<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hobbies', function (Blueprint $table) {
            $table->string('custom_log_label')->nullable()->after('estimated_cost');
        });
    }

    public function down(): void
    {
        Schema::table('hobbies', function (Blueprint $table) {
            $table->dropColumn('custom_log_label');
        });
    }
};
