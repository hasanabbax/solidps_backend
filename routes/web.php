<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hotels', 'HotelController@index');

Route::get('/flights', 'FlightController@index');
Route::get('/flights/create', 'FlightController@create');

Route::get('/airports', 'AirportController@index');
Route::get('/airlines', 'AirlineController@index');
Route::get('/routes', 'RouteController@index');
Route::get('/planes', 'PlaneController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
