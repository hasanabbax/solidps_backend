<?php

use Illuminate\Database\Seeder;

class MergingEurobookingsHRSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $eurobookingsHotels = DB::table('hotels_eurobookings_data')->get();

        $array = [];

        foreach ($eurobookingsHotels as $eurobookingsHotelsInstance) {

            $HRSHotels = DB::table('hotels_hrs_data')->get();

            foreach ($HRSHotels as $HRSHotelsInstance) {


/*                $lat1 = $eurobookingsHotelsInstance->latitude_google;
                $lng1 = $eurobookingsHotelsInstance->longitude_google;

                $lat2 = $HRSHotelsInstance->latitude_google;
                $lng2 = $HRSHotelsInstance->longitude_google;
*/
//                if (($lat1 == $lat2) && ($lng1 == $lng2)) {
                if ($eurobookingsHotelsInstance->name == $HRSHotelsInstance->name) {
                    echo $eurobookingsHotelsInstance->name . ' == ' . $HRSHotelsInstance->name . ' ' . Carbon\Carbon::now()->toDateTimeString() . "\n";
                }
//                }
            }
        }
    }
}
