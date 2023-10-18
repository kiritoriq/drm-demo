<?php

use Domain\Shared\Service\Models\Service;
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
        Schema::create('contractor_services', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(Service::class, 'service_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_services');
    }
};
