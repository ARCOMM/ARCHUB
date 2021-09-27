<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JoinStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('join_statuses')->insert([
            'text' => 'Pending',
            'permalink' => 'pending',
            'position' => 0,
            'protected' => true
        ]);

        DB::table('join_statuses')->insert([
            'text' => 'Approved',
            'permalink' => 'approved',
            'position' => 1,
            'protected' => true
        ]);

        DB::table('join_statuses')->insert([
            'text' => 'Declined',
            'permalink' => 'declined',
            'position' => 2,
            'protected' => true
        ]);

        DB::table('join_statuses')->insert([
            'text' => 'Blacklisted',
            'permalink' => 'blacklisted',
            'position' => 3,
            'protected' => true
        ]);

        DB::table('join_statuses')->insert([
            'text' => 'Outdated',
            'permalink' => 'outdated',
            'position' => 4,
            'protected' => false
        ]);
    }
}
