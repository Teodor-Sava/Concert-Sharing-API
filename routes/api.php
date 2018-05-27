<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {

    // ======================= CRUD Bands ==================================


    Route::get('/bands', 'BandsController@index');

    Route::get('/bands/{band}', 'BandsController@show');

    Route::post('/bands', 'BandsController@store');

    Route::patch('/bands/{band}', 'BandsController@update');
    // ======================= CRUD Concerts ==================================

    Route::get('/concerts', 'ConcertsController@index');

    Route::get('/concerts/{concert}', 'ConcertsController@show');

    // ======================= Concerts Requests CRUD==================================

    Route::get('/concert-requests/{user_id}','ConcertRequestController@showUserConcertRequests');

    Route::get('/concert-requests/{band_id}','ConcertRequestController@showBandConcertRequests');

    Route::post('/concert-requests/{concert_request}','ConcertRequestController@store');

    Route::patch('/concert-requests/{concert_request}','ConcertRequestController@store');


    // ======================= Different data =================================

    Route::get('/countries', 'CountriesController@index');

    Route::get('/genres', 'GenresController@index');
});
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});
