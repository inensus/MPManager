<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->boolean('active');
            $table->double('c_total');
            $table->string('c_total_unit');
            $table->double('c_newly_energy');
            $table->string('c_newly_energy_unit');
            $table->double('temperature_min');
            $table->double('temperature_max');
            $table->double('temperature_average');
            $table->string('temperature_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batteries', function (Blueprint $table) {
            //
        });
    }
}
