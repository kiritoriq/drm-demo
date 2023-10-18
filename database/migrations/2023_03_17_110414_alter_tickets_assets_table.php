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
        Schema::table('tickets_assets', function (Blueprint $table) {
            $table->foreignIdFor(\Domain\Shared\User\Models\BranchAsset::class, 'branch_asset_id')
                ->after('ticket_id')
                ->nullable()
                ->references('id')
                ->on('branches_assets')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets_assets', function (Blueprint $table) {
            //
        });
    }
};
