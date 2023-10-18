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
        Schema::table('tasks_assets', function (Blueprint $table) {
            $table->dropIfExists('tasks_assets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks_assets', function (Blueprint $table) {
            $table->dropIfExists('tasks_assets');
        });
    }
};
