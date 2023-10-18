<?php

use Domain\Shared\User\Models\Branch;
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
        Schema::create('branches_users', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class, 'branch_id')->constrained();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches_users');
    }
};
