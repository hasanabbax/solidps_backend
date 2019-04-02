<?php

use JonnyW\PhantomJs\Client as PhantomClient;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

use Illuminate\Database\Seeder;

class Rooms_hrs_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $dA = [];

    public function mainRun($hotel, $dA)
    {
        try {
            $this->dA = $dA;
            $this->dA['hotel_uid'] = $hotel->uid;
            $this->dA['hotel_hrs_id'] = $hotel->hrs_id;
            $this->dA['hotel_name'] = $hotel->name;
            $this->dA['city'] = $hotel->city;
            $this->dA['source'] = $hotel->source;

            $this->dA['proxy'] = 'proxy.proxycrawl.com:9000';
            $this->dA['timeOut'] = 40000;
            $this->dA['request_date'] = date("Y-m-d");
            $this->dA['count_access_denied'] = 0;
            $this->dA['count_unauthorized'] = 0;
            $this->dA['count_not_found'] = 0;
            $this->dA['count_!200'] = 0;
            $this->dA['count_!200b'] = 0;
            $this->dA['count_!200c'] = 0;
            $this->dA['full_break'] = false;

            Storage::makeDirectory('hrs/' . $this->dA['request_date']);

            while (strtotime($this->dA['start_date']) <= strtotime($this->dA['end_date'])) {
                $this->dA['check_in_date'] = $this->dA['start_date'];
                $this->dA['check_out_date'] = date("Y-m-d", strtotime("+1 day", strtotime($this->dA['start_date'])));
                foreach ($this->dA['adults'] as $adult) {
                    if ($this->dA['full_break'] == true) {
                        break 2;
                    }
                    $this->dA['adult'] = $adult;
                    $this->dA['hotel_url'] = "https://www.hrs.com/hotelData.do?hotelnumber=" . $this->dA['hotel_hrs_id'] . "&activity=offer&availability=true&l=en&customerId=413388037&forwardName=defaultSearch&searchType=default&xdynpar_dyn=&fwd=gbgCt&client=en&currency=" . $this->dA['currency'] . "&startDateDay=" . date("d", strtotime($this->dA['check_in_date'])) . "&startDateMonth=" . date("m", strtotime($this->dA['check_in_date'])) . "&startDateYear=" . date("Y", strtotime($this->dA['check_in_date'])) . "&endDateDay=" . date("d", strtotime($this->dA['check_out_date'])) . "&endDateMonth=" . date("m", strtotime($this->dA['check_out_date'])) . "&endDateYear=" . date("Y", strtotime($this->dA['check_out_date'])) . "&adults=$adult&singleRooms=" . (($adult == 1) ? 1 : 0) . "&doubleRooms=" . (($adult > 1) ? 1 : 0) . "&children=0";

                    restart2:
                    $crawler = $this->phantomRequest($this->dA['hotel_url']);
                    if ($crawler) {
                        $this->roomData($crawler);


                        try {
                            if (!empty($this->dA['all_rooms'])) {
                                if (is_array($this->dA['all_rooms'])) {
                                    if (!empty($this->dA['room_facilities'])) {
                                        foreach ($this->dA['all_rooms'] as $rooms) {
                                            foreach ($rooms as $room) {
                                                if (!empty($room['room']) && !empty($room['price'])) {
                                                    $room['room_type'] = ($this->dA['adult'] > 1) ? 'doubleroom' : 'singleroom';

                                                    $rid = $this->dA['request_date'] . $this->dA['check_in_date'] . $this->dA['check_out_date'] . $this->dA['hotel_name'] . $room['room'] . $room['room_type'] . $room['price']; //Requestdate + CheckInDate + CheckOutDate + HotelId + RoomName + number of adults
                                                    $rid = str_replace(' ', '', $rid);

                                                    if (DB::table('rooms_prices_hrs')->where('rid', '=', $rid)->doesntExist()) {
                                                        DB::table('rooms_prices_hrs')->insert([
                                                            'uid' => uniqid(),
                                                            's_no' => 1,
                                                            'price' => $room['price'],
                                                            'currency' => $this->dA['currency'],
                                                            'room' => $room['room'],
                                                            'room_type' => $room['room_type'],
                                                            'criteria' => $room['criteria'],
                                                            'basic_conditions' => serialize($room['room_basic_conditions']),
                                                            'photo' => $room['room_image'],
                                                            'short_description' => $room['room_short_description'],
                                                            'facilities' => (isset($this->dA['room_facilities']) ? serialize($this->dA['room_facilities']) : null),
                                                            'hotel_uid' => $this->dA['hotel_uid'],
                                                            'hotel_name' => $this->dA['hotel_name'],
                                                            'hotel_hrs_id' => $this->dA['hotel_hrs_id'],
                                                            'number_of_adults_in_room_request' => $this->dA['adult'],
                                                            'check_in_date' => $this->dA['check_in_date'],
                                                            'check_out_date' => $this->dA['check_out_date'],
                                                            'rid' => $rid,
                                                            'request_date' => $this->dA['request_date'],
                                                            'source' => $this->dA['source'],
                                                            'created_at' => DB::raw('now()'),
                                                            'updated_at' => DB::raw('now()')
                                                        ]);
                                                        $this->dA['count_unauthorized'] = 0;
                                                        $this->dA['count_access_denied'] = 0;
                                                        $this->dA['count_not_found'] = 0;
                                                        $this->dA['count_!200'] = 0;
                                                        $this->dA['count_!200b'] = 0;
                                                        $this->dA['count_!200c'] = 0;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ($this->dA['count_!200b'] < 2) {
                                            $this->dA['count_!200b']++;
                                            goto restart2;
                                        } else {
                                            Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/ignoreEmptyFacilities1a.log', 'url:' . $this->dA['hotel_url'] . ' ' . ';' . Carbon::now()->toDateTimeString() . "\n");
                                        }
                                    }
                                } else {
                                    if ($this->dA['count_!200c'] < 10) {
                                        $this->dA['count_!200c']++;
                                        goto restart2;
                                    } else {
                                        Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/ignoreEmptyRoomOrPrice2a.log', 'url:' . $this->dA['hotel_url'] . ' ' . ';' . Carbon::now()->toDateTimeString() . "\n");
                                    }
                                }
                            } else {
                                if ($this->dA['count_!200c'] < 10) {
                                    $this->dA['count_!200c']++;
                                    goto restart2;
                                } else {
                                    Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/ignoreEmptyRoomOrPrice2b.log', 'url:' . $this->dA['hotel_url'] . ' ' . ';' . Carbon::now()->toDateTimeString() . "\n");
                                }
                            }
                            $this->dA['all_rooms'] = null;

                        } catch (Exception $e) {
                            $this->catchException($e, 'ErrorDB');
                        }
                    }
                }
                $this->dA['start_date'] = date("Y-m-d", strtotime("+1 day", strtotime($this->dA['start_date'])));
            }
        } catch (Exception $e) {
            $this->catchException($e, 'errorMain');
        }
    }

    protected function catchException($e, $fileName)
    {
        Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/' . $fileName . '.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon::now()->toDateTimeString() . "\n");
        print($e->getMessage());
    }

    protected function phantomRequest($url)
    {
        try {
            restart:
            $client = PhantomClient::getInstance();
            $client->getEngine()->setPath(base_path() . '/bin/phantomjs');
//            $client->getEngine()->addOption('--load-images=false');
//            $client->getEngine()->addOption('--ignore-ssl-errors=true');
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
                if ($response->getStatus() != 0 && $response->getStatus() != 408) {
                    Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/ignoreBreakReason.log', 'url:' . $url . ' ;minor-break-reason4b:(getStatus())->' . $response->getStatus() . ';count_unauthorized:' . $this->dA['count_unauthorized'] . ';count_access_denied:' . $this->dA['count_access_denied'] . ' ' . Carbon::now()->toDateTimeString() . "\n");
                }
                if ($this->dA['full_break'] == false) {
                    if ($this->dA['count_!200'] > 200) {
                        Storage::append('hrs/' . $this->dA['request_date'] . '/' . $this->dA['city'] . '/BreakReason.log', 'url:' . $url . ' ;minor-break-reason4b:(getStatus())->' . $response->getStatus() . ';count_unauthorized:' . $this->dA['count_unauthorized'] . ';count_access_denied:' . $this->dA['count_access_denied'] . ' ' . Carbon::now()->toDateTimeString() . "\n");
                        $this->dA['full_break'] = true;
                    } else {
                        $this->dA['count_!200']++;
                        goto restart;
                    }
                } else {
                    return null;
                }
            }
        } catch (Exception $e) {
            $this->catchException($e, 'phantomRequestError');
        }
    }

    protected function roomData($crawler)
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
                $dr['price_cents'] = ($node->filter('td.roomPrice > div > h4.price.standalonePrice > sup')->count() > 0) ? $node->filter('td.roomPrice > div > h4.price.standalonePrice > sup')->text() : null;
                $dr['price'] = str_replace(array($dr['price_cents'], '€'), '', $dr['full_text_price']) . '.' . $dr['price_cents'];
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
}

