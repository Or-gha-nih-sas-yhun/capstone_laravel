<?php

namespace Database\Seeders;

use App\Models\Milestone;
use App\Support\DefaultRubrics;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Milestone::all() as $milestone) {
            DefaultRubrics::attachTo($milestone);
        }
    }
}