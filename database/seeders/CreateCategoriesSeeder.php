<?php

namespace Database\Seeders;

use Domain\Shared\User\Models\Category;
use Illuminate\Database\Seeder;

class CreateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Electrical', 'Plumbing', 'Inspection', 'Drainage', 'Air Conditioner', 'Kitchen Hot Equipment', 'Kitchen Cold Equipment', 'Others'];

        foreach ($categories as $category) {
            Category::query()
                ->updateOrCreate(
                    attributes: [
                        'name' => $category,
                    ]
                );
        }
    }
}
