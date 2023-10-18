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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Ticket::class, 'ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'sender_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
