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
        Schema::create('tasks_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Task::class)
                ->constrained();
            $table->foreignIdFor(\Domain\Shared\User\Models\BranchAsset::class, 'branch_asset_id')
                ->references('id')
                ->on('branches_assets')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks_assets');
    }
};
