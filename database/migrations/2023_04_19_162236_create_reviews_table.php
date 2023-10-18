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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'customer_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'contractor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Task::class)->constrained();
            $table->integer('stars')->default(0);
            $table->text('text_review');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
