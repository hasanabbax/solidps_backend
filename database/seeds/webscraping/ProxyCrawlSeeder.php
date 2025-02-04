<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use JonnyW\PhantomJs\Client as PhantomClient;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Database\Seeder;

class ProxyCrawlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $url = 'https://api.myip.com/';
        $url = 'https://www.hotelhunter.com/Hotels/Search?destination=place%3ABerlin&checkin=2019-07-13&checkout=2019-07-14&Rooms=1&adults_1=2&languageCode=EN&currencyCode=EUR#destination=place:Berlin&radius=0mi&checkin=2019-07-13&checkout=2019-07-14&Rooms=1&adults_1=2&pageSize=15&pageIndex=0&sort=Popularity-desc&showSoldOut=false&scroll=1431&HotelID=&mapState=expanded%3D0';
       /*
        $url = 'http://www.eurobookings.com/search.html?q=start:2019-04-05;end:2019-04-06;rmcnf:1[2,0];dsti:3023;dstt:1;dsts:Rome;frm:9;sort:0_desc;cur:EUR;stars:0;';
        $url = 'https://www.hrs.com/en/hotel/Vienna/d-45883/1#container=&locationId=45883&requestUrl=%2Fen%2Fhotel%2FVienna%2Fd-45883&showAlternates=false&toggle=&arrival=2019-04-14&departure=2019-04-15&lang=en&minPrice=false&roomType=double&singleRoomCount=0&doubleRoomCount=1';
        $proxy = 'proxy.proxycrawl.com:9000';*/


        /*$client = PhantomClient::getInstance();
        $client->getEngine()->addOption('--load-images=false');
        $client->getEngine()->addOption('--ignore-ssl-errors=true');
        $client->getEngine()->addOption("--proxy=http://" . $proxy);
        $client->isLazy(); // Tells the client to wait for all resources before rendering
        $request = $client->getMessageFactory()->createRequest($url);
        $response = $client->getMessageFactory()->createResponse();
        // Send the request
        $client->send($request, $response);
        $content2 = $response->getContent();
        $crawler = new Crawler($content2);



        dd($crawler->html());*/

        while (0 == 0) {
            $client = PhantomClient::getInstance();
            $client->getEngine()->setPath(base_path() . '/bin/phantomjs');
            $client->getEngine()->addOption('--load-images=false');
            $client->getEngine()->addOption('--ignore-ssl-errors=true');
//            $client->getEngine()->addOption("--proxy=http://" . $proxy);
            $client->isLazy(); // Tells the client to wait for all resources before rendering
            $request = $client->getMessageFactory()->createRequest($url);
//            $request->setTimeout(20000);
            $response = $client->getMessageFactory()->createResponse();
            // Send the request
            $client->send($request, $response);
            $crawler = new Crawler($response->getContent());

            if ($crawler->filter('body')->count()) {
                $crawler->filter('td')->each(function ($node) {
                    echo $node->text() . "\n";
                });

                echo $crawler->filter('div#SearchResultsBody')->text();

                Storage::put('hotelhunder.html', $crawler->html());
                dd('reached');
//                echo $crawler->text() . ' ' . Carbon::now()->toDateTimeString() . "\n";
            } else {
                echo 'empty : ' . $response->getStatus() . "\n";
            }
        }
    }
}
