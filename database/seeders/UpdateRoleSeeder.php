<?php

namespace Database\Seeders;

use Domain\Shared\User\Enums\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as RoleModel;

class UpdateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect(Role::cases())->map(fn ($role) => [
            'name' => $role->value,
            'guard_name' => 'web',
        ])->toArray();

        foreach ($roles as $role) {
            $checkRole = RoleModel::query()
                ->whereName($role['name'])
                ->first();

            if (blank($checkRole)) {
                RoleModel::create($role);
            }
        }
    }
}
