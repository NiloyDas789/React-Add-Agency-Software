<?php

namespace Database\Seeders;

use App\Models\RestrictedAni;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestrictedAniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RestrictedAni::factory(10)->create();
    }
}
