<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketCategoriesOutsourcing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //get ticketing table names
        $tableNames = config('tickets.table_names');
        Schema::table($tableNames['ticket_categories'], static function (Blueprint $table) {
            $table->boolean('out_source')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //get ticketing table names
        $tableNames = config('tickets.table_names');
        Schema::table($tableNames['ticket_categories'], static function (Blueprint $table) {
            $table->dropColumn('out_source')->default(0);
        });
    }
}
