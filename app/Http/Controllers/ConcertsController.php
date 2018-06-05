<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Http\Resources\ConcertResource;
use App\Http\Resources\ConcertsResource;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConcertsController extends Controller
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

        if (isset($queryParams['offset'])) {
            $offset = $queryParams['offset'];
        }

        if (isset($queryParams['limit'])) {
            $limit = $queryParams['limit'];
        }

        if (isset($queryParams['search'])) {
            $searchParams = $queryParams['search'];
            $limit = 10;
            $concerts = Concert::with('band', 'space', 'user')
                ->where('name', 'LIKE', "%{$searchParams}%")
                ->orderBy('concert_start', 'desc')
                ->paginate($limit);
        } else {
            $concerts = Concert::with('band', 'space', 'user')
                ->orderBy('concert_start', 'desc')
                ->paginate($limit);
        }


        return response()->json($concerts, 200);
    }

    public function showAllUserConcerts(Request $request, User $user)
    {
        $concerts = Concert::find(Ticket::where('user_id', $user->id)->pluck('concert_id')->toArray());

        if(count($concerts)<1){
            return response()->json('No concerts found', 404);
        }
        return response()->json($concerts, 200);
    }

    public function showUserUpcomingConcerts(Request $request, User $user)
    {
        $allconcerts = Concert::find(Ticket::where('user_id', $user->id)->pluck('concert_id')->toArray());
        $concerts = [];
        foreach ($allconcerts as $concert) {
            if ($concert->concert_start >= Carbon::today()->toDateString()) {
                $concerts[] = $concert;
            }
        }
        if(count($concerts)<1){
            return response()->json('No concerts found', 404);
        }
        return response()->json($concerts, 200);
    }

    public function showUserPastConcerts(Request $request, User $user)
    {
        $allconcerts = Concert::find(Ticket::where('user_id', $user->id)->pluck('concert_id')->toArray());
        $concerts = [];
        foreach ($allconcerts as $concert) {
            if ($concert->concert_start < Carbon::today()->toDateString()) {
                $concerts[] = $concert;
            }
        }

        return response()->json($concerts, 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $concert = new Concert();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Concert $concert)
    {
        ConcertResource::withoutWrapping();

        return response(new ConcertResource($concert));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
