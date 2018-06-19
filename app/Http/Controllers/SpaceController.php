<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Space;
use App\SpaceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
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
            $spaces = Space::where('name', 'LIKE', "%{$searchParams}%")
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        } else {
            $spaces = Space::orderBy('created_at', 'desc')
                ->paginate($limit);
        }


        return response()->json($spaces, 200);
    }

    public function store(Request $request)
    {
        $space = new Space();

        $space->name = $request->name;
        $space->description = $request->description;
        $space->lng = $request->lng;
        $space->lat = $request->lat;
        $space->user_id = auth()->user()->id;
        $space->save();

        return response()->json($space, 200);
    }

    public function show(Space $space)
    {
        return response()->json($space);
    }

    public function update(Request $request, Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space->update($request->all());

            return response()->json('Space update', 200);
        }
    }

    public function getConcertsForSpace(Space $space)
    {
        $upcomingConcerts = Concert::where('space_id', $space->id)
            ->where('concert_start', '>', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get();

        $pastConcerts = Concert::where('space_id', $space->id)
            ->where('concert_start', '<', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get();

        $data = [
            'upcoming_concerts' => $upcomingConcerts ? $upcomingConcerts : 'No upcoming concerts',
            'past_concerts' => $pastConcerts ? $pastConcerts : 'No past concerts'
        ];

        return response()->json($data, 200);

    }


}
