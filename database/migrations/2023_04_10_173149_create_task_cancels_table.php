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
        Schema::create('task_cancels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Task::class)->constrained();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class)->constrained();
            $table->string('reject_reason');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_cancels');
    }
};
