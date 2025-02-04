<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Event as EventResource;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventsAPIController extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('API_KEY');
    }

    //
    public function Events($rows, $apiKey, $city)
    {
        if ($apiKey == $this->apiKey) {
            $events = DB::table('events')
                ->where('city', '=', $city);
            if (isset($events)) {
                ($rows > 0) ? $events = $events->limit($rows) : null;
                $events = $events->get();
                return EventResource::collection($events);
            }
            dd('Error: Data Not Found : Events');
        } else {
            dd('Error: Incorrect API Key');
        }
    }
}
