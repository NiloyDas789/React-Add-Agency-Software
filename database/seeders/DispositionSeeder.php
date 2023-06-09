<?php

namespace Database\Seeders;

use App\Models\Disposition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DispositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispositions = ['Order'];

        foreach ($dispositions as $disposition) {
            Disposition::Insert([
                'title' => $disposition,
            ]);
        }
    }
}
