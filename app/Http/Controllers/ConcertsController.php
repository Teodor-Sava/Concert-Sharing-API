<?php

namespace App\Http\Controllers;

use App\Band;
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

    public function showBandUpcomingConcerts(Band $band)
    {
        $concerts = Concert::where('band_id', $band->id)->where('concert_start', '>', Carbon::now())->get();

        return response()->json($concerts, 200);
    }

    public function showBandPastConcerts(Band $band)
    {
        $concerts = Concert::where('band_id', $band->id)->where('concert_start', '<', Carbon::now())->get();

        return response()->json($concerts, 200);
    }

    public function showAllUserConcerts(Request $request, User $user)
    {
        $concerts = Concert::find(Ticket::where('user_id', $user->id)->pluck('concert_id')->toArray());

        if (count($concerts) < 1) {
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
        if (count($concerts) < 1) {
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
        if (!empty(Concert::where('name', $request->name)->first())) {
            return response()->json('Name already exists', 409);
        }
        $concert = new Concert();

        $concert->name = $request->name;
        $concert->total_tickets = isset($request->total_tickets) ? $request->total_tickets : null;
        $concert->available_tickets = isset($request->total_tickets) ? $request->total_tickets : null;
        $concert->concert_start = isset($request->concert_start) ? $request->concert_start : '';
        $concert->short_description = isset($request->short_description) ? $request->short_description : null;
        $concert->long_description = isset($request->long_description) ? $request->long_description : null;

        if ($request->get('image')) {

            $image = $request->get('image');
            $filename = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('image'))->resize(300, 300)->save(public_path('uploads/concert_pictures/') . $filename);
            $concert->poster_url = $filename;
        }
        $concert->user_id = auth()->user()->id;

        $concert->save();

        return response()->json('Concert created', 200);
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

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Concert $concert)
    {
        if (!empty(Concert::where('name', $request->name)->first())) {
            return response()->json('Name already exists', 409);
        }

        $concert->name = $request->name;
        $concert->total_tickets = isset($request->total_tickets) ? $request->total_tickets : null;
        $concert->available_tickets = isset($request->total_tickets) ? $request->total_tickets : null;
        $concert->concert_start = isset($request->concert_start) ? $request->concert_start : '';
        $concert->short_description = isset($request->short_description) ? $request->short_description : null;
        $concert->long_description = isset($request->long_description) ? $request->long_description : null;

        if ($request->get('image')) {

            $image = $request->get('image');
            $filename = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('image'))->resize(300, 300)->save(public_path('uploads/concert_pictures/') . $filename);
            $concert->poster_url = $filename;
        }
        $concert->user_id = auth()->user()->id;

        $concert->save();

        return response()->json('Concert created', 200);
    }

    public function buyConcertTicket(Request $request, Concert $concert)
    {
        $no_tickets = $request->no_tickets;

        if ($no_tickets > 0 && ($concert->available_tickets - $no_tickets > 0)) {
            $concert->available_tickets -= $no_tickets;


            if ($no_tickets > 1) {
                for ($i = 0; $i < $no_tickets; $i++) {
                    $ticket = new Ticket();
                    $ticket->user_id = auth()->user()->id;
                    $ticket->concert_id = $concert->id;
                    $ticket->price = $request->ticket_price;
                    $ticket->save();
                }
            } else {
                $ticket = new Ticket();
                $ticket->user_id = auth()->user()->id;
                $ticket->concert_id = $concert->id;
                $ticket->price = $request->ticket_price;
                $ticket->save();
            }

            return response()->json('The tickets have been purchased', 200);
        }
        return response()->json('Something went wrong with your purchase', 401);
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
