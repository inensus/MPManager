<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeterConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_consumptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meter_id');
            $table->double('total_consumption');
            $table->double('daily_consumption');
            $table->double('credit_on_meter');
            $table->date('reading_date');
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
        Schema::dropIfExists('meter_consumptions');
    }
}
