<?php

namespace App\Http\Controllers;

use App\Band;
use App\BandGenre;
use App\Concert;
use App\FavoriteBands;
use App\Http\Resources\BandResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class BandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = 20;

        $queryParams = $request->all();

        if (isset($queryParams['limit'])) {
            $limit = $queryParams['limit'];
        }

        if (isset($queryParams['search'])) {
            $searchParams = $queryParams['search'];
            $limit = 10;
            $bands = Band::with('genre', 'country')
                ->where('name', 'LIKE', "%{$searchParams}%")
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

        } else {
            $bands = Band::with('genre', 'country')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }
        return response()->json($bands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (!empty(Band::where('name', $request->name)->first())) {
            return response()->json('Name already exists', 409);
        }

        $band = new Band();
        $band->name = $request->name;
        $band->country_id = $request->country_id;
        $band->no_members = $request->no_members;
        $band->founded_at = new Carbon($request->founded_at);
        $band->band_requests = $request->band_requests;
        $band->price = $request->price;
        $band->short_description = $request->short_description;
        $band->long_description = $request->long_description;

        if ($request->get('image')) {

            $image = $request->get('image');
            $filename = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('image'))->resize(300, 300)->save(public_path('uploads/band_pictures/') . $filename);
            $band->image_url = 'http://127.0.0.1:8000/uploads/band_pictures/'.$filename;
        }

        $band->user_id = auth()->user()->id;
        $band->save();

        if (!empty($request->genres)) {
            foreach ($request->genres as $genre) {
                BandGenre::create(array(
                    'band_id' => $band->id,
                    'genre_id' => $genre
                ));
            }
        }

        return response()->json($band, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Band $band)
    {
        return response(new BandResource($band));
    }

    public function showUserBands(User $user, Band $band)
    {
        $bands = Band::where('user_id', $user->id);
        print_r($bands);
        die();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Band $band)
    {
        $band->name = $request->name;
        $band->country_id = $request->country_id;
        $band->no_members = $request->no_members;
        $band->founded_at = new Carbon($request->founded_at);
        $band->band_requests = $request->band_requests;
        $band->price = $request->price;
        $band->short_description = $request->short_description;
        $band->long_description = $request->long_description;
        $band->image_url = $request->image_url;
        $band->user_id = auth()->user()->id;
        $band->save();

        if (!empty($request->genres)) {
            foreach ($request->genres as $genre) {
                BandGenre::create(array(
                    'band_id' => $band->id,
                    'genre_id' => $genre
                ));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function getAllRequestsForBandsAdmin()
    {
        $user_id = auth()->user()->id;
//        print_r($user_id);
//        die();
        $bands = Band::where('bands.user_id', $user_id)
            ->leftJoin('concert_requests', 'concert_requests.band_id', '=', 'bands.id')
            ->select('bands.id', 'bands.name',
                DB::raw('count(case when concert_requests.band_status= "rejected" then 1 else null end) as rejected_requests'),
                DB::raw('count(case when concert_requests.band_status = "accepted" then 1 else null end) as accepted_requests'),
                DB::raw('count(case when concert_requests.band_status = "pending" then 1 else null end) as pending_requests'))
            ->groupBy('bands.id', 'bands.name')
            ->get();

        if ($bands) {
            return response()->json($bands, 200);
        }
        return response()->json('No bands found', 404);
    }

    public function getDoneDealsForBandAdmin(Band $band)
    {
        $user_id = auth()->user()->id;

        $concerts = Concert::where('concerts.user_id', $user_id)
            ->join('concert_requests', 'concert_requests.concert_id', '=', 'concerts.id')
//            ->join('concert_requests','concert_requests.band_id', '=','')
            ->select('concerts.id', 'concerts.name', 'concerts.concert_start')
            ->where('concert_requests.band_id', $band->id)
            ->where('concert_requests.concert_status', 'accepted')
            ->where('concert_requests.band_status', 'accepted')
            ->groupBy('concerts.id', 'concerts.name', 'concerts.concert_start')
            ->paginate();
//            ->get();
        if ($concerts) {
            return response()->json($concerts, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function addBandToFavorites(Band $band)
    {
        $user = auth()->user();
        if (!empty(FavoriteBands::where('user_id', $user->id)->where('band_id', $band->id)->first())) {
            return response()->json('Band is already a favorite', 404);
        }
        $favorite_band = new FavoriteBands();
        $favorite_band->user_id = $user->id;
        $favorite_band->band_id = $band->id;
        $favorite_band->save();

        $response = ['message' => 'Band added to favorites'];
        return response()->json($response, 200);
    }

    public function removeBandFromFavorites(Band $band)
    {
        $user = auth()->user();
        FavoriteBands::where('user_id', $user->id)->where('band_id', $band->id)->delete();

        $response = ['message' => 'Band removed from favorites'];
        return response()->json($response, 200);
    }

    public function showFavoriteBands()
    {
        $user = auth()->user();
        if ($favorite_bands_ids = FavoriteBands::where('user_id', $user->id)->orderBy('created_at')->pluck('band_id')) {
            $bands = Band::with('country', 'genre')->whereIn('id', $favorite_bands_ids)->paginate(20);

            return response()->json($bands, 200);
        }
        return response()->json('No favorite bands found', 404);

    }

    public function checkIfBandIsFavorite(Band $band)
    {
        $user = auth()->user();

        if (!empty($favorite_band = FavoriteBands::where('user_id', $user->id)->where('band_id', $band->id)->first())) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }

    public function destroy($id)
    {
        //
    }

}
