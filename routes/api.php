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

    Route::get('/admin/bands', 'BandsController@getAllRequestsForBandsAdmin');

    Route::post('/bands', 'BandsController@store');

    Route::patch('/bands/{band}', 'BandsController@update');


    // ======================= Band Favorites ==================================

    Route::get('/band-favorites/user/current_user', 'BandsController@showFavoriteBands');

    Route::get('/band-favorites/band/{band}/status', 'BandsController@checkIfBandIsFavorite');

    Route::post('/band-favorites/band/{band}', 'BandsController@addBandToFavorites');

    Route::delete('/band-favorites/band/{band}', 'BandsController@removeBandFromFavorites');
    // ======================= CRUD Concerts ==================================

    Route::get('/concerts', 'ConcertsController@index');

    Route::get('/concerts/{concert}', 'ConcertsController@show');

    Route::get('/concerts/user/current_user', 'ConcertsController@showAllLoggedInUserConcerts');

    Route::get('/concerts/user/{user}/upcoming', 'ConcertsController@showUserUpcomingConcerts');

    Route::get('/concerts/user/{user}/past', 'ConcertsController@showUserPastConcerts');

    Route::get('/concerts/upcoming/band/{band}', 'ConcertsController@showBandUpcomingConcerts');

    Route::get('/concerts/past/band/{band}', 'ConcertsController@showBandPastConcerts');

    Route::post('/concerts', 'ConcertsController@store');

    Route::post('/concerts/{concert}/buy-tickets', 'ConcertsController@buyConcertTicket');

    // ======================= Concerts Requests CRUD==================================

    Route::get('/concert-requests', 'ConcertRequestController@index');

    Route::get('/concert-requests/{concert_request}', 'ConcertRequestController@show');

    Route::get('/concert-requests/user/{user}', 'ConcertRequestController@showUserConcertRequests');

    Route::get('/admin/bands/{band}/pending-requests', 'ConcertRequestController@getPendingRequestForBandsAdmin');

    Route::get('/concert-requests/band/{band}/accepted-requests', 'ConcertRequestController@showAcceptedRequestsForBandsAdmin');

    Route::get('/concert-requests/band/{band}/rejected-requests', 'ConcertRequestController@showRejectedRequestsForBandsAdmin');

    Route::get('/concert-requests/concert/{concert}', 'ConcertRequestController@showRequestsForConcertAdmin');


    Route::post('/concert-requests', 'ConcertRequestController@store');

    Route::post('/concert-requests/{concert_request}/band/{band}/accept', 'ConcertRequestController@acceptConcertRequestByBand');

    Route::post('/concert-requests/{concert_request}/band/{band}/decline', 'ConcertRequestController@acceptConcertRequestByBand');

    Route::post('/concert-requests/{concert_request}/band/confirm', 'ConcertRequestController@confirmBandForConcert');

    Route::patch('/concert-requests/{concert_request}', 'ConcertRequestController@update');

    // ======================= Space CRUD==================================

    Route::get('/spaces', 'SpaceController@index');

    Route::post('/spaces', 'SpaceController@store');

    Route::patch('/spaces/{space}', 'SpaceController@update');

    Route::delete('/spaces/{space}', 'SpaceController@destroy');

    // ======================= Space Requests CRUD==================================

    Route::get('/space-requests/{concert_request}', 'SpaceRequestController@show');

    Route::get('/space-requests/user/{user}', 'SpaceRequestController@showUserConcertRequests');

    Route::get('/space-requests/space/{space}', 'SpaceRequestController@showBandConcertRequests');

    Route::post('/space-requests', 'SpaceRequestController@store');

    Route::patch('/space-requests/{concert_request}', 'SpaceRequestController@update');

    // ======================= Reviews Requests CRUD==================================

    Route::get('/concerts/{concert}/reviews', 'ReviewsController@index');

    Route::get('/concerts/{concert}/reviews/{review}', 'ReviewsController@show');

    Route::post('/concerts/{concert}/reviews', 'ReviewsController@store');

    Route::patch('/concerts/{concert}/reviews/{review}', 'ReviewsController@update');

    Route::delete('/concerts/{concert}/reviews/{review}', 'ReviewsController@destroy');

    // ======================= User Details CRUD==================================

    Route::get('/users/{user}/details', 'UserDetailsController@getUserDetailsByUserID');

    Route::post('/users/details', 'UserDetailsController@store');

    Route::patch('/users/{user}/details', 'UserDetailsController@update');
    // ======================= Different data =================================

    Route::get('/countries', 'CountriesController@index');

    Route::get('/genres', 'GenresController@index');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});
