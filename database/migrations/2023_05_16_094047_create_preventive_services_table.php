<?php

use Domain\Shared\User\Models\BranchAsset;
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
        Schema::create('preventive_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BranchAsset::class, 'branch_asset_id')
                ->references('id')
                ->on('branches_assets')
                ->cascadeOnDelete();
            $table->date('next_service_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_services');
    }
};
