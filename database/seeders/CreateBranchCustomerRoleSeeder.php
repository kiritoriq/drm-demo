<?php

namespace Database\Seeders;

use Domain\Shared\User\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateBranchCustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()
            ->firstOrCreate(
                attributes: [
                    'name' => 'Branch Customer'
                ],
                values: [
                    'guard_name' => 'web'
                ]
            );
    }
}
