<?php

use Domain\Shared\State\Models\State;
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
        Schema::create('contractor_coverages', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(State::class, 'state_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_coverages');
    }
};
