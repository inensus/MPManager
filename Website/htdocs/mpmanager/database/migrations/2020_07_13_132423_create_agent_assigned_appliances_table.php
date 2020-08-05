<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentAssignedAppliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_assigned_appliances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('agent_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('appliance_type_id');
            $table->decimal('cost', 12,4);
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
        Schema::dropIfExists('agent_assigned_appliances');
    }
}
