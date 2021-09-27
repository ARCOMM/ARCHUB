<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->insert([
            'subject' => 'Thank you for your application!',
            'content' => 'Your application has been submitted and is awaiting review from an admin. Applications are usually reviewed within 2-3 days of submission. Once your application is reviewed you will receive an additional email as to whether or not it was accepted or declined.',
            'locked' => true
        ]);

        DB::table('email_templates')->insert([
            'subject' => 'Your application has been declined',
            'content' => '',
            'locked' => false
        ]);

        DB::table('email_templates')->insert([
            'subject' => 'Your application has been approved',
            'content' => '',
            'locked' => false
        ]);

        DB::table('email_templates')->insert([
            'subject' => 'Your application requires an interview',
            'content' => '',
            'locked' => false
        ]);
    }
}
