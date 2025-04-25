<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $departments = [
            ['id' => 1, 'name' => 'Human Resources',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Finance',            'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Marketing',          'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Sales',              'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'IT',                 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Operations',         'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Customer Support',   'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('departments')->insert($departments);
    }
}
