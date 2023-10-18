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
        Schema::table('branches_assets', function (Blueprint $table) {
            $table->string('preventive_cycle')->nullable()->after('assignee_id');
            $table->double('preventive_service')->default(0)->after('preventive_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches_assets', function (Blueprint $table) {
            //
        });
    }
};
