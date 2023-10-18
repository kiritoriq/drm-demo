<?php

use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Priority;
use Domain\Ticket\Enums\Status;
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
        Schema::create(table: 'tickets', callback: function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->string('subject');
            $table->text('description');
            $table->foreignIdFor(User::class, 'raised_by_id');
            $table->foreignIdFor(User::class, 'assignee_id')->nullable();
            $table->string('priority')
                ->default(Priority::Low->value)
                ->nullable();
            $table->string('status')
                ->default(Status::New->value);
            $table->date('due_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'tickets');
    }
};
