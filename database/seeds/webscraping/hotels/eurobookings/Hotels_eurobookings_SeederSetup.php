<?php

use Illuminate\Database\Seeder;

class Hotels_eurobookings_SeederSetup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = DB::table('hotels_basic_data_for_gathering')->where('source', '=', 'eurobookings.com')->inRandomOrder()->get();

        $euroBooking = new Hotels_eurobookings_Seeder();
        for ($k = -1; $k <= 5; $k++) {
            foreach ($cities as $instance) {
                $instance = (array)$instance;
                $instance['start_date'] = '2019-04-12';
                $instance['end_date'] = '2019-04-12';
                $instance['currency'] = 'EUR';
                $instance['k'] = $k;
                $euroBooking->run($instance);
            }
        }
    }
}
