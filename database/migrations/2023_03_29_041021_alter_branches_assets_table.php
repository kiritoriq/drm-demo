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
            $table->string('asset_code')->after('branch_id');
            $table->text('description')->nullable()->after('name');
            $table->string('brand')->nullable()->after('description');
            $table->string('model')->nullable()->after('brand');
            $table->string('year_make', 10)->nullable()->after('model');
            $table->foreignIdFor(\Domain\Shared\Area\Models\Area::class)
                ->nullable()
                ->constrained();
            $table->foreignIdFor(\Domain\Shared\User\Models\Category::class)
                ->nullable()
                ->constrained();
            $table->foreignIdFor(\Domain\Shared\User\Models\User::class, 'assignee_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->text('preventive_services')->nullable()->after('assignee_id');
            $table->string('vendor_purchased_from')->nullable()->after('preventive_services');
            $table->date('warranty_expiry_date')->nullable()->after('vendor_purchased_from');
            $table->string('year_purchased')->nullable()->after('warranty_expiry_date');
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
