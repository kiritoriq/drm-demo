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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Ticket::class, 'ticket_id')
                ->references('id')
                ->on('tickets')
                ->cascadeOnDelete();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'raised_by_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->double('total_amount')->default(0);
            $table->tinyInteger('is_client_agreed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
