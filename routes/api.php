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

    Route::get('/bands/user/{user}', 'BandsController@showUserBands');

    Route::post('/bands', 'BandsController@store');

    Route::patch('/bands/{band}', 'BandsController@update');

    // ======================= Band Favorites ==================================

    Route::get('/bands/favorites/{user}', 'BandsController@showFavoriteBands');

    Route::post('/bands/favorites/{band}/user/{user}', 'BandsController@addBandToFavorites');

    Route::delete('/bands/favorites/{band}/user/{user}', 'BandsController@removeBandFromFavorites');
    // ======================= CRUD Concerts ==================================

    Route::get('/concerts', 'ConcertsController@index');

    Route::get('/concerts/{concert}', 'ConcertsController@show');

    Route::get('/concerts/users/{user}/all', 'ConcertsController@showAllUserConcerts');

    Route::get('/concerts/users/{user}/upcoming', 'ConcertsController@showUserUpcomingConcerts');

    Route::get('/concerts/users/{user}/past', 'ConcertsController@showUserUpcomingConcerts');

    // ======================= Concerts Requests CRUD==================================

    Route::get('/concert-requests/{concert_request}', 'ConcertRequestController@show');

    Route::get('/concert-requests/user/{user}', 'ConcertRequestController@showUserConcertRequests');

    Route::get('/concert-requests/band/ {band}', 'ConcertRequestController@showBandConcertRequests');

    Route::post('/concert-requests', 'ConcertRequestController@store');

    Route::patch('/concert-requests/{concert_request}', 'ConcertRequestController@update');


    // ======================= Space Requests CRUD==================================

    Route::get('/concert-requests/{concert_request}', 'ConcertRequestController@show');

    Route::get('/concert-requests/user/{user}', 'ConcertRequestController@showUserConcertRequests');

    Route::get('/concert-requests/band/ {band}', 'ConcertRequestController@showBandConcertRequests');

    Route::post('/concert-requests', 'ConcertRequestController@store');

    Route::patch('/concert-requests/{concert_request}', 'ConcertRequestController@update');

    // ======================= Different data =================================

    Route::get('/countries', 'CountriesController@index');

    Route::get('/genres', 'GenresController@index');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});
