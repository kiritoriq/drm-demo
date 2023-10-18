<?php

namespace Database\Seeders;

use Domain\Shared\State\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StoreStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = json_decode(Storage::get(path: 'states.json'));

        foreach ($states as $state) {
            State::create([
                'name' => $state
            ]);
        }
    }
}
