<?php

namespace App\Http\Controllers;

use App\Band;
use App\BandGenre;
use App\Http\Resources\BandResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        return response($bands);
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
        $band = new Band();

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
    public function destroy($id)
    {
        //
    }
}
