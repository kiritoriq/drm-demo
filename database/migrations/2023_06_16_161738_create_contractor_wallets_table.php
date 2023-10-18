<?php

use Domain\Shared\Ticket\Models\Task;
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
        Schema::create('contractor_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(Task::class, 'task_id')->constrained();
            $table->double('amount', 10, 2)->default(0);
            $table->tinyInteger('is_redeemed')->default(0);
            $table->foreignIdFor(User::class, 'redeemed_by')->nullable();
            $table->dateTime('redeemed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_wallets');
    }
};
