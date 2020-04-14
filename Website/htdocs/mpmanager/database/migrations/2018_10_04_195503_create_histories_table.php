<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('target'); // it contains a the target information
            $table->text('content'); //its a stringified representation of what happened
            $table->string('action', 6); //the type of the entry (create, update, delete)
            $table->string('field', 20)->nullable(); //the type of the entry (create, update, delete)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
}
