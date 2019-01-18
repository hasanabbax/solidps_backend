<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('Not Available');
            $table->string('address')->default('Not Available');
            $table->string('city')->default('Not Available');
            $table->string('country')->default('Not Available');
            $table->string('phone')->default('Not Available');
            $table->text('website')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->binary('all_data')->nullable();
            $table->string('source')->default('Not Available');
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
        Schema::dropIfExists('hotels');
    }
}
