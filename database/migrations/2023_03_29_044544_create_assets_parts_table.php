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
        Schema::create('assets_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Domain\Shared\User\Models\BranchAsset::class, 'branch_asset_id')
                ->references('id')
                ->on('branches_assets')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_parts');
    }
};
