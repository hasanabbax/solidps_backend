<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightsAfklmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('flights_afklm', function (Blueprint $table) {
            $table->string('uid')->unique()->primary();
            $table->unsignedInteger('s_no');

            $table->double('flights_duration')->default(0);
            $table->double('number_of_flights')->default(0);

            /*$table->string('arrival_date')->nullable();
            $table->string('arrival_time')->nullable();
            $table->string('arrival_day')->nullable();
            $table->string('departure_date')->nullable();
            $table->string('departure_time')->nullable();
            $table->string('departure_day')->nullable();
            $table->string('destination_airport')->nullable();
            $table->string('destination_city')->nullable();
            $table->string('destination_city_code')->nullable();
            $table->string('destination_airport_iata')->nullable();
            $table->string('origin_airport')->nullable();
            $table->string('origin_city')->nullable();
            $table->string('origin_city_code')->nullable();
            $table->string('origin_airport_iata')->nullable();
            $table->string('carrier_name')->nullable();
            $table->string('carrier_code')->nullable();
            $table->string('number_of_stops')->nullable();
            $table->string('equipmenttype_code')->nullable();
            $table->string('equipmenttype_name')->nullable();
            $table->string('equipmenttype_acvCode')->nullable();
            $table->string('cabin_class')->nullable();
            $table->string('flight_carrier_name')->nullable();
            $table->string('flight_carrier_code')->nullable();
            $table->string('selling_class_code')->nullable();
            $table->string('farebase_code')->nullable();
            $table->double('transfer_time')->default(0);*/

            $table->longText('flights_data')->nullable();
            $table->double('display_price')->default(0);
            $table->double('total_price')->default(0);
            $table->double('accuracy')->default(0);
            $table->string('passenger_1_type')->nullable();
            $table->double('passenger_1_fare')->default(0);
            $table->double('passenger_1_taxes')->default(0);
            $table->string('passenger_2_type')->nullable();
            $table->double('passenger_2_fare')->default(0);
            $table->double('passenger_2_taxes')->default(0);
            $table->string('passenger_3_type')->nullable();
            $table->double('passenger_3_fare')->default(0);
            $table->double('passenger_3_taxes')->default(0);
            $table->string('passenger_4_type')->nullable();
            $table->double('passenger_4_fare')->default(0);
            $table->double('passenger_4_taxes')->default(0);
            $table->double('number_of_seats_available')->default(0);
            $table->boolean('flexibility_waiver')->default(false);
            $table->string('currency')->nullable();
            $table->string('display_type')->nullable();
            $table->string('fid')->unique();

            $table->string('origin_airport_initial');//initial origin
            $table->string('destination_airport_final');//final desitination

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
        Schema::dropIfExists('flights_afklm');
    }
}
