<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticket_settings')->insert([
            'name' => 'Trello',
            'api_token' => '9818ff631bd5a6cffa9d224675e1eb77de7df01f3ccaa92776d7d663a25c3c8f',
            'api_url' => 'https://api.trello.com/1',
            'api_key' => '0d9d80dbc26ccf55b5c605d910d9f27h',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
