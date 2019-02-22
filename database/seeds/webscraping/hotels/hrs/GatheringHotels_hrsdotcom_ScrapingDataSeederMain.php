<?php

use Goutte\Client as GoutteClient;

//use JonnyW\PhantomJs\Client as PhantomClient;
//use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Seeder;

class GatheringHotels_hrsdotcom_ScrapingDataSeederMain extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function mainRun($dataArray)
    {
        session_start();

        global $city, $country, $checkInDate, $checkOutDate, $cityId, $currency;

        $city = $dataArray['city'];
        $currency = $dataArray['currency'];
        $country = $dataArray['country'];
        $cityId = $dataArray['city_id'];

//        $date = '2019-02-20';
        $date = $dataArray['start_date'];
        $end_date = $dataArray['end_date']; //last checkin date hogi last me
        //

        $client = new GoutteClient();

        while (strtotime($date) <= strtotime($end_date)) {


            $checkInDate = $date;

            $checkOutDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));

            for ($i = 1; $i < 10000; $i++) {

                try {

                    $url = "https://www.hrs.com/en/hotel/$city/d-$cityId/$i#container=&locationId=$cityId&requestUrl=%2Fen%2Fhotel%2F$city%2Fd-$cityId&showAlternates=false&toggle=&arrival=$checkInDate&departure=$checkOutDate&lang=en&minPrice=false&roomType=double&singleRoomCount=0&doubleRoomCount=1&_=1550832580038";
                    $crawler = $client->request('GET', $url);
                    echo $url . "\n";

                    Storage::append('hrs/' . $city . '/url.log', $url . ' ' . Carbon\Carbon::now()->toDateTimeString() . "\n");
                    $response = $client->getResponse();

                    if ($response->getStatus() == 200) {


                        $crawler->filter('a.sw-hotel-list__link')->each(function ($node) {
                            $tempData = $node->attr('data-gtm-click');

//                        $da['hotel_id'] = preg_replace('/[^0-9]/', '', $da['link']);

                            $dh['hotel_id'] = json_decode($tempData)->ecommerce->click->products[0]->id;

                            $adult = 1;
                            global $currency;
                            $url1 = "https://www.hrs.com/hotelData.do?hotelnumber=" . $dh['hotel_id'] . "&activity=offer&availability=true&l=en&customerId=413388037&forwardName=defaultSearch&searchType=default&xdynpar_dyn=&fwd=gbgCt&client=en&currency=$currency&startDateDay=22&startDateMonth=02&startDateYear=2019&endDateDay=23&endDateMonth=02&endDateYear=2019&adults=$adult&singleRooms=1&doubleRooms=0&children=0#priceAnchor";
                            $adults = 2;
                            $url2 = "https://www.hrs.com/hotelData.do?hotelnumber=" . $dh['hotel_id'] . "&activity=offer&availability=true&l=en&customerId=413388037&forwardName=defaultSearch&searchType=default&xdynpar_dyn=&fwd=gbgCt&client=en&currency=$currency&startDateDay=22&startDateMonth=02&startDateYear=2019&endDateDay=23&endDateMonth=02&endDateYear=2019&adults=$adults&singleRooms=0&doubleRooms=1&children=0#priceAnchor";

                            try {


                                $client = new GoutteClient();
                                $crawler = $client->request('GET', $url1);
                                $crawler2 = $client->request('GET', $url2);

                                $dr['all_rooms'][] = $crawler2->filter('table#basket > tbody > tr ')->each(function ($node) {
                                    $dr['room'] = ($node->filter('td.roomOffer > div > h4')->count() > 0) ? $node->filter('td.roomOffer > div > h4')->text() : null;
                                    $dr['room_type'] = ($node->count() > 0) ? $node->attr('data-roomtype') : null;
                                    $dr['room_short_description'] = ($node->filter('td.roomOffer > div > p')->count() > 0) ? $node->filter('td.roomOffer > div > p')->text() : null;
                                    $dr['price'] = ($node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->last()->text() : null;
                                    $dr['criteria'] = ($node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->last()->text() : null;
                                    foreach ($dr as $key => $value) {
                                        if (!is_array($value)) {
                                            $dr[$key] = trim(str_replace(array("\r", "\n", "\t"), '', $value));
                                        }
                                        if (empty($value)) {
                                            unset($dr[$key]);
                                        }
                                    }
                                    return $dr;
                                });

                                $dr['all_rooms'][] = $crawler->filter('table#basket > tbody > tr ')->each(function ($node) {
                                    $dr['room'] = ($node->filter('td.roomOffer > div > h4')->count() > 0) ? $node->filter('td.roomOffer > div > h4')->text() : null;
                                    $dr['room_type'] = ($node->count() > 0) ? $node->attr('data-roomtype') : null;
                                    $dr['room_short_description'] = ($node->filter('td.roomOffer > div > p')->count() > 0) ? $node->filter('td.roomOffer > div > p')->text() : null;
                                    $dr['price'] = ($node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->last()->text() : null;
                                    $dr['criteria'] = ($node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->last()->text() : null;
                                    foreach ($dr as $key => $value) {
                                        if (!is_array($value)) {
                                            $dr[$key] = trim(str_replace(array("\r", "\n", "\t"), '', $value));
                                        }
                                        if (empty($value)) {
                                            unset($dr[$key]);
                                        }
                                    }
                                    return $dr;
                                });

                                $dh['hotel_name'] = ($crawler->filter('div#detailsHead > h2 > span.title')->count() > 0) ? $crawler->filter('div#detailsHead > h2 > span.title')->text() : null;
                                $dh['hotel_address'] = ($crawler->filter('address.hotelAdress')->count() > 0) ? $crawler->filter('address.hotelAdress')->text() : null;

                                $da['source'] = 'hrs.com';

                                global $city, $cityId, $country;

                                $hid = 'hotel' . $dh['hotel_name'] . 'address' . $dh['hotel_address'];
                                $dh['hid'] = str_replace(' ', '', $hid);
                                if (DB::table('hotels_hrs')->where('hid', '=', $dh['hid'])->doesntExist()) {
                                    $dh['hotel_uid'] = uniqid();
                                    DB::table('hotels_hrs')->insert([
                                        'uid' => $dh['hotel_uid'],
                                        's_no' => 1,
                                        'name' => $dh['hotel_name'],
                                        'address' => $dh['hotel_address'],
                                        'city' => $city,
                                        'city_id_on_hrs' => $cityId,
                                        'country' => $country,
                                        'hid' => $dh['hid'],
                                        'source' => $da['source'],
                                        'created_at' => DB::raw('now()'),
                                        'updated_at' => DB::raw('now()')
                                    ]);
                                    echo Carbon\Carbon::now()->toDateTimeString() . ' Completed hotel-> ' . $dh['hotel_name'] . "\n";
                                } else {
                                    $resultHid = DB::table('hotels_hrs')->select('uid')->where('hid', '=', $dh['hid'])->get();
                                    $dh['hotel_uid'] = $resultHid[0]->uid;
                                    echo Carbon\Carbon::now()->toDateTimeString() . ' Existeddd hotel-> ' . $dh['hotel_name'] . "\n";
                                }

                                foreach ($dr['all_rooms'] as $rooms) {

                                    foreach ($rooms as $room) {


                                        if (isset($room['room']) || isset($room['price'])) {

                                            global $checkOutDate, $checkInDate, $currency;

                                            $requestDate = date("Y-m-d");
                                            if (isset($room['room_type'])) {
                                                if ($room['room_type'] == 'singleroom') {
                                                    $adults = 1;
                                                }
                                                if ($room['room_type'] == 'doubleroom') {
                                                    $adults = 2;
                                                }
                                            }
                                            $rid = $requestDate . $checkInDate . $checkOutDate . $dh['hotel_name'] . $room['room'] . $room['room_type'] . $room['room_short_description'] . $room['price']; //Requestdate + CheckInDate + CheckOutDate + HotelId + RoomName + number of adults
                                            $rid = str_replace(' ', '', $rid);
                                            if (DB::table('rooms_prices_hrs')->where('rid', '=', $rid)->doesntExist()) {


                                                DB::table('rooms_prices_hrs')->insert([
                                                    'uid' => uniqid(),
                                                    's_no' => 1,
                                                    'price' => $room['price'],
                                                    'currency' => $currency,
                                                    'room' => $room['room'],
                                                    'room_type' => $room['room_type'],
                                                    'criteria' => $room['criteria'],
                                                    'short_description' => $room['room_short_description'],
                                                    'hotel_uid' => $dh['hotel_uid'],
                                                    'hotel_name' => $dh['hotel_name'],
                                                    'number_of_adults_in_room_request' => $adults,
                                                    'check_in_date' => $checkInDate,
                                                    'check_out_date' => $checkOutDate,
                                                    'rid' => $rid,
                                                    'request_date' => $requestDate,
                                                    'source' => $da['source'],
                                                    'created_at' => DB::raw('now()'),
                                                    'updated_at' => DB::raw('now()')
                                                ]);
                                                echo Carbon\Carbon::now()->toDateTimeString() . ' Completed in-> ' . $checkInDate . ' out-> ' . $checkOutDate . ' hotel-> ' . $dh['hotel_name'] . "\n";
                                            } else {
                                                echo Carbon\Carbon::now()->toDateTimeString() . ' Existeddd in-> ' . $checkInDate . ' out-> ' . $checkOutDate . ' hotel-> ' . $dh['hotel_name'] . "\n";
                                            }
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                global $city;
                                Storage::append('hrs/' . $city . '/errorSingleHotelAndDb.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon\Carbon::now()->toDateTimeString() . "\n");
                                print($e->getMessage());
                            }


                        });
                    }
                    if ($response->getStatus() != 200) {
                        break;
                    }
                } catch (\Exception $e) {
                    global $city;
                    Storage::append('hrs/' . $city . '/errorMain.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon\Carbon::now()->toDateTimeString() . "\n");
                    print($e->getMessage());
                }
            }


            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
    }
}
