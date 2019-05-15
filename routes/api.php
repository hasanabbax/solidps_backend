<?php

//use Illuminate\Http\Request;
//
///*
//|--------------------------------------------------------------------------
//| API Routes
//|--------------------------------------------------------------------------
//|
//| Here is where you can register API routes for your application. These
//| routes are loaded by the RouteServiceProvider within a group which
//| is assigned the "api" middleware group. Enjoy building your API!
//|
//*/
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::get('hotels&get={get}&apiKey={apiKey}&city={city}', 'ApiController@HRSHotels');

Route::get('roomsprices&get={get}&apiKey={apiKey}&hotelid={hotel_id}&datefrom={dateFrom}&dateto={dateTo}', 'ApiController@HRSRoomsPrices');

Route::get('competitorsavgprices&get={get}&apiKey={apiKey}&hotelid={hotel_id}&datefrom={dateFrom}&dateto={dateTo}&competitorsid={competitorsid}', 'ApiController@HRSHotelsCompetitorsAvgPrices');

Route::get('competitorsroomsprices&get={get}&apiKey={apiKey}&hotelid={hotel_id}&datefrom={dateFrom}&dateto={dateTo}&competitorsid={competitorsid}', 'ApiController@HRSHotelsCompetitorsRoomsPrices');

Route::get('competitorsroomsavgprices&get={get}&apiKey={apiKey}&hotelid={hotel_id}&datefrom={dateFrom}&dateto={dateTo}&competitorsid={competitorsid}', 'ApiController@HRSHotelsCompetitorsRoomsAvgPrices');

Route::get('events&get={get}&apiKey={apiKey}&city={city}', 'ApiController@Events');




Route::get('competitorsavgpricesapex&get={get}&apiKey={apiKey}&hotelid={hotel_id}&datefrom={dateFrom}&dateto={dateTo}&competitorsid={competitorsid}', 'ApiController@HRSHotelsCompetitorsAvgPricesApex');
