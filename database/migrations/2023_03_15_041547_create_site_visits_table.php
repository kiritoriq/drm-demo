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
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\Ticket\Models\Ticket::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->dateTime('visit_date');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
