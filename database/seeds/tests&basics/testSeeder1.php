<?php

use GuzzleHttp\Client;

use Illuminate\Database\Seeder;

class testSeeder1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

    }
}

/*
        $events = DB::table('events')->get();

        foreach ($events as $event){

            DB::table('events')
                ->where('uid', $event->uid)
                ->update([
                    'event_date' => unserialize($event->all_data)->dates->start->localDate
                ]);
        }
        */


/*
$client = new Client();

        $crawler = $client->request('GET', 'https://www.hoteleasyreservations.com/her5th/(S(tbzb5vh2t4limkkftcdiskdd))/her/pren1.aspx?_ga=2.199339693.2049225604.1549354551-1627713159.1549354551&EASYCHATSESSION=');


//        $crawler->filter('.flexible-row > .cellflex')->each(function ($node,$i=0) {
        $crawler->filter('.cellflex')->each(function ($node, $i = 0) {
            print $node->text() . "\n";

            $day = $node->filter('.weekDayFlex')->text();
            $month = $node->filter('.ddmonthFlex')->text();
            $price = $node->filter('#spanPriceFlex0')->text();

            dd($price);
        });

//        dd($crawler);
 */

/*
        $j = 0;
        $duplicates = DB::table('cities')
            ->select('name', 'latitude', 'longitude', DB::raw('COUNT(*) as `count`'))
            ->groupBy('name', 'latitude', 'longitude')
            ->having('count', '>', 1)
            ->get();


        foreach ($duplicates as $duplicate) {

            dd($duplicate);
        }
*/

/*
        for ($i = 1; $i <= 250000; $i++) {
            if (DB::table('flights_data')->where('id', $i)->exists()) {
                $results = DB::table('flights_data')->where('id', $i)->get();


                foreach ($results as $instance) {

                    DB::table('flights_test')->insert([
                        'all_data' => gzcompress($instance->all_data),
                        'source' => 'developer.laminardata.aero',
                        'created_at' => DB::raw('now()'),
                        'updated_at' => DB::raw('now()')
                    ]);
                }
            }
        }
        */

/*
for ($i = 1; $i <= 250000; $i++) {
    $i = rand(0,250000);
    if (DB::table('flights_data')->where('id', $i)->exists()) {
        $results = DB::table('flights_data')->where('id', $i)->get();

        foreach ($results as $instance) {

            if (DB::table('flights')->where('flight_id', unserialize($instance->all_data)->id )->exists()) {
                echo 'yes ' . unserialize($instance->all_data)->id . '  ' .  $i .  "\n ";
            } else {
                echo 'no ' . unserialize($instance->all_data)->id . '  ' .  $i . "\n ";
            }
        }
    }
}
*/


/*
for ($i = 1; $i <= 500000; $i++) {
    $i = rand(0,250000);
    if (DB::table('hotels')->where('id', $i)->exists()) {
        $results = DB::table('hotels')->where('id', $i)->get();

        foreach ($results as $instance) {

            DB::table('flights')
                ->where('id', $instance->id)
                ->update([

                ]);
        }
    }
}
*/

/*
for ($i = 1; $i <= 600000; $i++) {
    if (DB::table('hotels')->where('id', $i)->exists()) {

        $results = DB::table('hotels')->where('id', 430422)->get();

        foreach ($results as $instance) {

            dd(unserialize(gzuncompress($instance->all_data)));


            if (!empty(unserialize($instance->all_data_detailed)->phone_number)) {
                $phone = unserialize($instance->all_data_detailed)->phone_number;
            } else {
                if (!empty(unserialize($instance->all_data_detailed)->international_phone_number)) {
                    $phone = unserialize($instance->all_data_detailed)->international_phone_number;
                }
            }

            DB::table('hotels')->insert([
                'name' => isset(unserialize($instance->all_data_detailed)->name) ? unserialize($instance->all_data_detailed)->name : null,
                'address' => isset(unserialize($instance->all_data_detailed)->address) ? unserialize($instance->all_data_detailed)->address : null,
                'city' => isset($instance->location) ? $instance->location : null,
                'country' => $country,
                'phone' => isset($phone) ? $phone : null,
                'website' => isset(unserialize($instance->all_data_detailed)->website) ? unserialize($instance->all_data_detailed)->website : null,
                'latitude' => isset(unserialize($instance->all_data_detailed)->lat) ? unserialize($instance->all_data_detailed)->lat : null,
                'longitude' => isset(unserialize($instance->all_data_detailed)->lng) ? unserialize($instance->all_data_detailed)->lng : null,
                'all_data' => gzcompress($instance->all_data),
                'source' => 'tour-pedia.org/api/',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()')
            ]);
        }
    }
}
*/