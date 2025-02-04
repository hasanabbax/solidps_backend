<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsHrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels_hrs', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->text('photo')->nullable();
            $table->unsignedInteger('hrs_id')->nullable()->index();
            $table->tinyInteger('stars')->nullable();
            $table->string('city')->nullable();
            $table->unsignedInteger('city_id_on_hrs')->nullable()->index();
            $table->string('country_code')->nullable()->index();
            $table->string('ratings_on_hrs')->nullable();
            $table->string('ratings_text_on_hrs')->nullable();
            $table->string('total_number_of_ratings_on_hrs')->nullable();
            $table->float('ratings_on_google')->nullable()->index();
            $table->unsignedInteger('total_number_of_ratings_on_google')->nullable()->index();
            $table->longText('location_details')->nullable();
            $table->longText('surroundings_of_the_hotel')->nullable();
            $table->longText('sports_leisure_facilities')->nullable();
            $table->longText('nearby_airports')->nullable();
            $table->longText('details')->nullable();
            $table->longText('facilities')->nullable();
            $table->longText('in_house_services')->nullable();
            $table->double('latitude_hrs')->nullable();
            $table->double('longitude_hrs')->nullable();
            $table->double('latitude_google')->nullable();
            $table->double('longitude_google')->nullable();
            $table->string('phone')->nullable();
            $table->text('website')->nullable();
            $table->text('hotel_url_on_hrs')->nullable();
            $table->string('hid', 255)->unique();
            $table->longText('all_data_google')->nullable();
            $table->string('source')->nullable();

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
        Schema::dropIfExists('hotels_hrs');
    }
}
