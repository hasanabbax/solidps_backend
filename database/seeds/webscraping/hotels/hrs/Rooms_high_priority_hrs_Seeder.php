<?php

use JonnyW\PhantomJs\Client as PhantomClient;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Seeder;

class Rooms_high_priority_hrs_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $dA = [];

    public function run($dA)
    {
        try {
            $this->dA = $dA;

            $this->dA['proxy'] = 'proxy.proxycrawl.com:9000';
            $this->dA['timeOut'] = 8000;
            $this->dA['request_date'] = date("Y-m-d");
            $this->dA['count_!200'] = 0;
            $this->dA['count_408&0'] = 0;
            $this->dA['noFacilitiesFound'] = 0;
            $this->dA['count_noPriceFound'] = 0;
//            $this->dA['full_break'] = false;

            if (!File::exists(storage_path() . '/app/hrs/' . $this->dA['request_date'] . '/')) {
                Storage::makeDirectory('hrs/' . $this->dA['request_date']);
            }

            $hotelCompetitorsIds = DB::table('competitors')->select('hotel_id')->distinct()->get();
            $hotelCompetitorsIds = json_decode(json_encode($hotelCompetitorsIds), true);

            $hotelOwnersIds = DB::table('users')->select('hotel_id')->distinct()->get();
            $hotelOwnersIds = json_decode(json_encode($hotelOwnersIds), true);

            $hotelManualIds = DB::table('hotel_ids_for_data_gathering')->select('hotel_id')->distinct()->get();
            $hotelManualIds = json_decode(json_encode($hotelManualIds), true);


            $selectedHotels = array_unique(array_merge($hotelCompetitorsIds, $hotelOwnersIds, $hotelManualIds), SORT_REGULAR);

            shuffle($selectedHotels);

            foreach ($selectedHotels as $hotelInstance) {

                $hotels = DB::table('hotels_hrs')->select('id', 'hrs_id', 'city')->where('id', '=', $hotelInstance['hotel_id'])->get();

                if (count($hotels) == 1) {
                    $this->dA['hotel_id'] = $hotels[0]->id;
                    $this->dA['hotel_hrs_id'] = $hotels[0]->hrs_id;
                    $this->dA['city'] = $hotels[0]->city;


                    foreach ($this->dA['adults'] as $adult) {
//                    if ($this->dA['full_break'] == true) {
//                        break 2;
//                    }
                        $this->dA['adult'] = $adult;
                        $this->dA['request_url'] = "https://www.hrs.com/hotelData.do?hotelnumber=" . $this->dA['hotel_hrs_id'] .
                            "&activity=offer&availability=true&l=en&customerId=413388037&forwardName=defaultSearch&searchType=default&xdynpar_dyn=&fwd=gbgCt&client=en&currency=" .
                            $this->dA['currency'] . "&startDateDay=" . date("d", strtotime($this->dA['check_in_date'])) . "&startDateMonth=" .
                            date("m", strtotime($this->dA['check_in_date'])) . "&startDateYear=" . date("Y", strtotime($this->dA['check_in_date'])) .
                            "&endDateDay=" . date("d", strtotime($this->dA['check_out_date'])) . "&endDateMonth=" . date("m", strtotime($this->dA['check_out_date'])) .
                            "&endDateYear=" . date("Y", strtotime($this->dA['check_out_date'])) . "&adults=$adult&singleRooms=" . (($adult == 1) ? 1 : 0) . "&doubleRooms=" .
                            (($adult > 1) ? 1 : 0) . "&children=0";

                        restart2:
                        $crawler = $this->phantomRequest($this->dA['request_url']);

                        if ($crawler) {
                            $this->roomData($crawler);

                            try {
                                if (!empty($this->dA['all_rooms'])) {
                                    if (is_array($this->dA['all_rooms'])) {

                                        foreach ($this->dA['all_rooms'] as $rooms) {
                                            foreach ($rooms as $room) {
                                                if (!empty($room['room']) && !empty($room['price'])) {

                                                    $room['room_type'] = ($this->dA['adult'] > 1) ? 'doubleroom' : 'singleroom';

                                                    $rid = 'hrs' . $this->dA['hotel_hrs_id'] . $room['room'] . $room['room_type']
                                                        . $this->dA['adult'] . //HotelHRSId + RoomName + roomType + room Short D + criteria without numbers or currencies + number of adults + hrstag
                                                        substr(preg_replace('/[0-9.]+/', '', $room['criteria']), 0, 60) .
                                                        substr($room['room_short_description'], 0, 60);
                                                    $rid = substr(str_replace(' ', '', $rid), 0, 254);

                                                    $r = DB::table('rooms_hrs')->select('id')->where('rid', '=', $rid)->get();

                                                    if (count($r)) {
                                                        $r_id = $r[0]->id;
                                                    } else {
                                                        $this->roomDataFacilities($crawler);
                                                        $r_id = $this->insertRoomsDataIntoDB($room, $rid);
                                                    }

                                                    $this->insertRoomsPricesDataIntoDB($room, $r_id);
                                                }
                                            }
                                        }

                                    }
                                } else {
                                    if ($this->dA['count_noPriceFound'] < 2) {
                                        $this->dA['count_noPriceFound']++;
                                        goto restart2;
                                    }
                                }
                                $this->dA['all_rooms'] = null;

                            } catch (Exception $e) {
                                $this->catchException($e, 'ErrorDB');
                            }
                        }
                    }
                }

            }
        } catch (Exception $e) {
            $this->catchException($e, 'errorMain');
        }
    }

    protected function catchException($e, $fileName)
    {
        Storage::append('hrs/' . $this->dA['request_date'] . '/' . (isset($this->dA['city']) ? $this->dA['city'] : 'CityNotFound') . '/' . $fileName . '.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon::now()->toDateTimeString() . "\n");
        print($e->getMessage());
    }

    protected function insertRoomsDataIntoDB($room, $rid)
    {
        $r_id = DB::table('rooms_hrs')->insertGetId([
            'room' => $room['room'],
            'room_type' => $room['room_type'],
            'criteria' => $room['criteria'],
            'basic_conditions' => serialize($room['room_basic_conditions']),
            'photo' => $room['room_image'],
            'short_description' => $room['room_short_description'],
            'facilities' => (isset($this->dA['room_facilities']) ? serialize($this->dA['room_facilities']) : null),
            'hotel_id' => $this->dA['hotel_id'],
            'rid' => $rid,
            'created_at' => DB::raw('now()'),
            'updated_at' => DB::raw('now()')
        ]);

        return $r_id;
    }

    protected function insertRoomsPricesDataIntoDB($room, $r_id)
    {
        $room['price'] = $room['price'] . '.' . $room['cents'];
        DB::table('prices_hrs')->insert([
            'price' => $room['price'],
            'currency' => $room['currency'],
            'number_of_adults_in_room_request' => $this->dA['adult'],
            'check_in_date' => $this->dA['check_in_date'],
            'check_out_date' => $this->dA['check_out_date'],
            'basic_conditions' => serialize($room['room_basic_conditions']),
            'request_url' => $this->dA['request_url'],
            'room_id' => $r_id,
            'request_date' => $this->dA['request_date'],
            'html_price' => $room['full_html_price'],
            'created_at' => DB::raw('now()'),
            'updated_at' => DB::raw('now()')
        ]);
        $this->dA['count_!200'] = 0;
        $this->dA['count_408&0'] = 0;
        $this->dA['noFacilitiesFound'] = 0;
        $this->dA['count_noPriceFound'] = 0;

    }

    protected function phantomRequest($url)
    {
        try {
            restart:
            $client = PhantomClient::getInstance();
            $client->getEngine()->setPath(base_path() . '/bin/phantomjs');
            $client->getEngine()->addOption('--load-images=false');
            $client->getEngine()->addOption('--ignore-ssl-errors=true');
//            $client->getEngine()->addOption("--proxy=http://" . $this->dA['proxy'][count($this->dA['proxy']) - 1]);
            $client->getEngine()->addOption("--proxy=http://" . $this->dA['proxy']);
            $client->isLazy(); // Tells the client to wait for all resources before rendering
            $request = $client->getMessageFactory()->createRequest($url);
            $request->setTimeout($this->dA['timeOut']);
            $response = $client->getMessageFactory()->createResponse();
            // Send the request
            $client->send($request, $response);
            $crawler = new Crawler($response->getContent());

            if ($response->getStatus() == 200) {
                return $crawler;
            } else {
//                if ($this->dA['full_break'] == false) {
                if ($this->dA['count_!200'] > 3) {
                    Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/minorBreakReasonA.log', 'url:' . $url . ' ;minor-break-reason4b:(getStatus())->' . $response->getStatus() . ' ' . Carbon::now()->toDateTimeString() . "\n");
                    return null;
                } elseif ($this->dA['count_408&0'] > 12) {
//                        Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/BreakReasonB.log', 'url:' . $url . ' ;minor-break-reason4b:(getStatus())->' . $response->getStatus() . ' ' . Carbon::now()->toDateTimeString() . "\n");
                    return null;
//                        $this->dA['full_break'] = true;
                } else {
                    if ($response->getStatus() != 0 && $response->getStatus() != 408) {
                        $this->dA['count_!200']++;
                    } else {
                        $this->dA['count_408&0']++;
                    }
                    goto restart;
                }
//                }
            }
        } catch (Exception $e) {
            $this->catchException($e, 'phantomRequestError');
        }
    }

    protected function roomData($crawler)
    {

        if ($crawler->filter('table#basket > tbody > tr')->count() > 0) {
            $this->dA['all_rooms'][] = $crawler->filter('table#basket > tbody > tr')->each(function ($node) {
                $dr['room'] = ($node->filter('td.roomOffer > div > h4')->count() > 0) ? $node->filter('td.roomOffer > div > h4')->text() : null;
                $dr['room_image'] = ($node->filter('td.roomOffer > div.imageWrap > img')->count() > 0) ? $node->filter('td.roomOffer > div.imageWrap > img')->attr('src') : null;
                $dr['room_basic_conditions'] = ($node->filter('td.roomOffer > div > ul.checkListSmall > li')->count() > 0) ? $node->filter('td.roomOffer > div > ul.checkListSmall > li')->each(function ($node) {
                    return ($node->count() > 0) ? $node->text() : null;
                }) : null;
                $dr['room_short_description'] = ($node->filter('td.roomOffer > div > p')->count() > 0) ? $node->filter('td.roomOffer > div > p')->text() : null;
//                                                $dr['price'] = ($node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tfoot > tr > td.price')->last()->text() : null;
//                                                $dr['price'] = ($node->filter('td.roomPrice > div > h4')->count() > 0) ? $node->filter('td.roomPrice > div > h4')->text() : null;
                $dr['full_text_price'] = ($node->filter('td.roomPrice > div > h4.price.standalonePrice')->count() > 0) ? $node->filter('td.roomPrice > div > h4.price.standalonePrice')->text() : null;
                $dr['full_html_price'] = ($node->filter('td.roomPrice > div > h4.price.standalonePrice')->count() > 0) ? $node->filter('td.roomPrice > div > h4.price.standalonePrice')->html() : null;
                $dr['cents'] = ($node->filter('td.roomPrice > div > h4.price.standalonePrice > sup')->count() > 0) ? $node->filter('td.roomPrice > div > h4.price.standalonePrice > sup')->text() : null;
                $dr['currency'] = str_replace(array(',', '.', ' '), '', preg_replace('/[0-9]+/', '', $dr['full_text_price']));
                $dr['price'] = preg_replace('/' . trim($dr['cents']) . '$/', '', preg_replace('/[^0-9.]/', '', str_replace(' ', '', $dr['full_text_price'])));

//                                                $dr['criteria'] = ($node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->count() > 0) ? $node->filter('td.roomPrice > div > div > table.data > tbody > tr > td > span')->last()->text() : null;
                $dr['criteria'] = ($node->filter('td.roomPrice > div > div.supplements')->count() > 0) ? $node->filter('td.roomPrice > div > div.supplements')->text() : null;
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
        }
    }

    protected function roomDataFacilities($crawler)
    {
        if ($crawler->filter('div.jsAmenities.equipement.col33')->count() > 0) {

            $crawler->filter('div.jsAmenities.equipement.col33')->each(function ($node) {
                if ($node->filter('h5')->count() > 0) {
                    if ($node->filter('h5')->text() == 'Room facilities') {
                        $this->dA['room_facilities'] = ($node->filter('li')->count() > 0) ? $node->filter('li')->each(function ($node) {
                            return trim($node->text());
                        }) : null;
                    }
                }
            });
        }
    }
}

