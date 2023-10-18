<?php

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
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
        Schema::create('ticket_assignees', function (Blueprint $table) {
            $table->foreignIdFor(Ticket::class, 'ticket_id');
            $table->foreignIdFor(User::class, 'assignee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_assignees');
    }
};
