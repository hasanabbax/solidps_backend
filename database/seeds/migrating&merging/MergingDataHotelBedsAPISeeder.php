<?php

use Illuminate\Database\Seeder;

class MergingDataHotelBedsAPISeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $j = 0;
        for ($i = 1; $i <= 200000; $i++) {

            $results = DB::table('hotel_beds')->where('id', $i)->get();
            if (!empty($results[0])) {
                foreach ($results as $instance) {

                    if (isset(unserialize($instance->all_data)->phones)) {

                        foreach (unserialize($instance->all_data)->phones as $componentInstance) {
                            if (!is_array($componentInstance)) {
                                if ($componentInstance->phoneType == 'PHONEHOTEL') {
                                    $phone = $componentInstance->phoneNumber;
                                } else {
                                    if ($componentInstance->phoneType == 'PHONEBOOKING') {
                                        $phone = $componentInstance->phoneNumber;
                                    }
                                }
//
                            }
                        }
                    }

                    if (isset($instance->country_code)) {
                        $country = DB::table('countries')->where('country_code', '=', $instance->country_code)->get();
                    }

                    DB::table('hotels_uncompressed')->insert([
                        'uid' => uniqid(),
                        's_no' => ++$j,
                        'name' => isset($instance->name) ? $instance->name : null,
                        'address' => isset($instance->address) ? $instance->address : null,
                        'city' => isset($instance->city) ? $instance->city : null,
                        'country' => isset($country[0]->name) ? $country[0]->name : null,
                        'phone' => isset($phone) ? $phone : null,
                        'website' => isset($instance->website) ? $instance->website : null,
                        'latitude' => isset(unserialize($instance->all_data)->coordinates->latitude) ? unserialize($instance->all_data)->coordinates->latitude : null,
                        'longitude' => isset(unserialize($instance->all_data)->coordinates->longitude) ? unserialize($instance->all_data)->coordinates->longitude : null,
                        'all_data' => $instance->all_data,
                        'source' => 'developer.hotelbeds.com',
                        'created_at' => DB::raw('now()'),
                        'updated_at' => DB::raw('now()')
                    ]);
                    echo 'hotel_beds->hotels_uncompressed ' . $j . "\n";
                }
            }
        }


    }
}