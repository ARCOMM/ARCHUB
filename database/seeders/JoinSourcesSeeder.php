<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JoinSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = [
            'Unknown',
            'YouTube',
            '/r/FindAUnit',
            'Unit Spreadsheet',
            'BI Forums',
            'Search Engine',
            'Friend Suggestion',
            'Other',
            'ArmaClans.com',
            'Arma 3 Units',
            'Twitter'
        ];

        foreach ($sources as $source) {
            DB::table('join_sources')->insert([
                'name' => $source
            ]);
        }
    }
}
