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
        Schema::create('branches_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\User\Models\Branch::class, 'branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();
            $table->string('name');
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches_assets');
    }
};
