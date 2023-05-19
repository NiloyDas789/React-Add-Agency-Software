<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);

        if (app()->isLocal()) {
            // $this->call(StationSeeder::class);
            // $this->call(QualificationSeeder::class);
            $this->call(DispositionSeeder::class);
            // $this->call(StateSeeder::class);
            // $this->call(ProviderSeeder::class);
            // $this->call(TollFreeNumberSeeder::class);
            // $this->call(RestrictedAniSeeder::class);
        }
    }
}
