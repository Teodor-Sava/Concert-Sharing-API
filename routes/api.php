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

    Route::get('/concerts/user/{user}/all', 'ConcertsController@showAllUserConcerts');

    Route::get('/concerts/user/{user}/upcoming', 'ConcertsController@showUserUpcomingConcerts');

    Route::get('/concerts/user/{user}/past', 'ConcertsController@showUserUpcomingConcerts');

    Route::get('/concerts/band/{band}/upcoming', 'ConcertsController@showBandUpcomingConcerts');

    Route::get('/concerts/band/{band}/past', 'ConcertsController@showBandPastConcerts');

    Route::post('/concerts', 'ConcertsController@store');

    Route::post('/concerts/buy-tickets/{concert}', 'ConcertsController@buyConcertTicket');

    // ======================= Concerts Requests CRUD==================================

    Route::get('/concert-requests', 'ConcertRequestController@index');

    Route::get('/concert-requests/{concert_request}', 'ConcertRequestController@show');

    Route::get('/concert-requests/user/{user}', 'ConcertRequestController@showUserConcertRequests');

    Route::get('/concert-requests/band/{band}/pending-requests', 'ConcertRequestController@showPendingRequestForBandsAdmin');

    Route::get('/concert-requests/band/{band}/accepted-requests', 'ConcertRequestController@showAcceptedRequestsForBandsAdmin');

    Route::get('/concert-requests/band/{band}/rejected-requests', 'ConcertRequestController@showRejectedRequestsForBandsAdmin');

    Route::get('/concert-requests/concert/{concert}', 'ConcertRequestController@showRequestsForConcertAdmin');

    Route::get('/con-req','ConcertRequestController@getAllRequestsForBandsAdmin');

    Route::post('/concert-requests', 'ConcertRequestController@store');

    Route::post('/concert-requests/band/confirm/{concert_request}', 'ConcertRequestController@confirmBandForConcert');

    Route::patch('/concert-requests/{concert_request}', 'ConcertRequestController@update');

    // ======================= Space CRUD==================================

    Route::get('/spaces', 'SpaceController@index');

    Route::post('/spaces', 'SpaceController@store');

    Route::patch('/spaces/{space}', 'SpaceController@update');

    // ======================= Space Requests CRUD==================================

    Route::get('/space-requests/{concert_request}', 'SpaceRequestController@show');

    Route::get('/space-requests/user/{user}', 'SpaceRequestController@showUserConcertRequests');

    Route::get('/space-requests/space/{space}', 'SpaceRequestController@showBandConcertRequests');

    Route::post('/space-requests', 'SpaceRequestController@store');

    Route::patch('/space-requests/{concert_request}', 'SpaceRequestController@update');

    // ======================= Different data =================================

    Route::get('/countries', 'CountriesController@index');

    Route::get('/genres', 'GenresController@index');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});
