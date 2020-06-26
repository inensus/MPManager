<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppliancePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appliance_people', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appliance_type_id');
            $table->integer('person_id');
            $table->integer('total_cost');
            $table->integer('rate_count');
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
        Schema::dropIfExists('appliance_people');
    }
}
