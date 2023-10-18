<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $adminUser = ['admin@admin.com'];

        foreach ($adminUser as $admin) {
            try {
                $user = \Domain\Shared\User\Models\User::firstOrCreate(
                    ['email' => $admin],
                    [
                        'name' => $admin,
                        'password' => bcrypt('password'),
                    ]);
                $user->assignRole('Admin');
            } catch (\Exception $e) {
                throw new \Exception($e, $e->getCode(), $e);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
