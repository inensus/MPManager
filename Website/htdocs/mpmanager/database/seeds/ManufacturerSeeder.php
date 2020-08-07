<?php


use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('manufacturers')->insert([
            'name' => 'Calin Meters STS',
            'website' => 'http://www.calinmeter.com/',
            'api_name' => 'CalinApi',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
