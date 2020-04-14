<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePVSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_v_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('mini_grid_id');
            $table->integer('node_id');
            $table->string('device_id');
            $table->double('daily');
            $table->string('daily_unit');
            $table->double('total');
            $table->string('total_unit');
            $table->double('new_generated_energy');
            $table->string('new_generated_energy_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_v_s');
    }
}
