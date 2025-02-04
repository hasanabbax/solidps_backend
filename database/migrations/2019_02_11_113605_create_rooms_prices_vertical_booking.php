<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsPricesVerticalBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('z_ignore_rooms_prices_vertical_booking', function (Blueprint $table) {
            $table->string('uid')->unique()->primary();
            $table->unsignedInteger('s_no');
            $table->string('display_price')->nullable();
            $table->string('room')->nullable();
            $table->text('room_short_description')->nullable();
            $table->text('room_description')->nullable();
            $table->text('room_facilities')->nullable();
            $table->text('room_rates_based_on_offers')->nullable();
            $table->string('number_of_adults_in_room_request')->nullable();
            $table->string('hotel_uid')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('hotel_address')->nullable();
            $table->string('hotel_city')->nullable();
            $table->string('hotel_phone')->nullable();
            $table->string('hotel_email')->nullable();
            $table->string('hotel_website')->nullable();
            $table->string('chain_website')->nullable();
            $table->string('check_in_date')->nullable();
            $table->string('check_out_date')->nullable();
            $table->string('rid')->unique();
            $table->string('request_date');
            $table->string('source');
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
        Schema::dropIfExists('z_ignore_rooms_prices_vertical_booking');
    }
}
