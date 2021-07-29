<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClusterMetaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cluster_meta_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cluster_id');
            $table->integer('mini_grid_id');
            $table->integer('energy_capacity');
            $table->integer('connected_meters');
            $table->integer('registered_customers');
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
        Schema::dropIfExists('cluster_meta_data');
    }
}
