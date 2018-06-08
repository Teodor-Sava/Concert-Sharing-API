<?php

namespace App\Http\Controllers;

use App\Space;
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
            $concerts = Space::where('name', 'LIKE', "%{$searchParams}%")
                ->paginate($limit);
        } else {
            $concerts = Space::paginate($limit);
        }

        return response()->json($concerts, 200);
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

        return response()->json('Space created', 200);
    }

    public function update(Request $request, Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
           $space->update($request->all());

            return response()->json('Space update', 200);
        }
    }
}
