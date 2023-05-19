<?php

namespace Database\Seeders;

use App\Models\TollFreeNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TollFreeNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TollFreeNumber::factory(10)->create();
    }
}
