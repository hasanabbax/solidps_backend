<?php

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;

use Illuminate\Database\Seeder;

class luminatiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
//        echo 'To enable your free eval account and get CUSTOMER, YOURZONE and '
//            .'YOURPASS, please contact sales@luminati.io';

        $url='http://example.com';
        $username = 'lum-customer-hl_4d865891-zone-static-route_err-pass_dyn';
        $password = 'azuuy61773vi';
        $port = 22225;
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
        $session = mt_rand();
        $super_proxy = 'zproxy.lum-superproxy.io';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username-session-$session:$password");
        $result = curl_exec($curl);
        curl_close($curl);
        if ($result) {
            echo $result;
        }


        $goutteClient = new GoutteClient();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
            'cookies' => true

        ));
        $goutteClient->setClient($guzzleClient);
        $crawler = $goutteClient->request('GET', $url);
        $response = $goutteClient->getResponse();

        dd($crawler->filter('div')->text());
    }
}
