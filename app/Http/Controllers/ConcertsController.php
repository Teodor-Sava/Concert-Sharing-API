<?php

namespace App\Http\Controllers;

use App\Band;
use App\Concert;
use App\Http\Resources\ConcertResource;
use App\Space;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

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
            $concerts = Concert::where('concert_public', true)
                ->where('name', 'LIKE', "%{$searchParams}%")
                ->with('band', 'space', 'user')
                ->orderBy('concert_start', 'desc')
                ->paginate($limit);
        } else {
            $concerts = Concert::with('band', 'space', 'user')
                ->where('concert_public', true)
                ->orderBy('concert_start', 'desc')
                ->paginate($limit);
        }


        return response()->json($concerts, 200);
    }

    public function showBandUpcomingConcerts(Band $band)
    {
        $concerts = Concert::where('band_id', $band->id)
            ->where('concert_start', '>', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get(['id', 'name', 'concert_start', 'total_tickets', 'available_tickets']);
        if (count($concerts) > 0) {
            return response()->json($concerts, 200);
        }
        return response()->json(false, 200);
    }

    public function showBandPastConcerts(Band $band)
    {
        $concerts = Concert::where('band_id', $band->id)
            ->with('space')
            ->where('concert_start', '<', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get()
            ->pluck('id', 'name', 'concert_start', 'total_tickets', 'available_tickets');

        return response()->json($concerts, 200);
    }

    public function getSpaceUpcomingConcerts(Space $space)
    {
        $concerts = Concert::where('space_id', $space->id)
            ->where('concert_start', '>', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get(['id', 'name', 'concert_start', 'total_tickets', 'available_tickets']);
        if (count($concerts) > 0) {
            return response()->json($concerts, 200);
        }
        return response()->json(false, 200);
    }

    public function getSpacePastConcerts(Space $space)
    {
        $concerts = Concert::where('space_id', $space->id)
            ->with('band')
            ->where('concert_start', '<', Carbon::now())
            ->orderBy('concert_start', 'desc')
            ->get()
            ->pluck('id', 'name', 'concert_start', 'total_tickets', 'available_tickets');

        return response()->json($concerts, 200);
    }

    public function showAllLoggedInUserConcerts(Request $request)
    {
        $user = auth()->user();
        $users_concerts = Ticket::where('user_id', $user->id)->distinct()->pluck('concert_id')->toArray();
        $concerts = Concert::with('band', 'space', 'user')
            ->whereIn('id', $users_concerts)
            ->orderBy('concert_start', 'desc')
            ->paginate();

        if (count($concerts) < 1) {
            return response()->json('No concerts found', 404);
        }
        return response()->json($concerts, 200);
    }

    public function getAllRequestsForConcertsAdmin()
    {
        $user_id = auth()->user()->id;
//        print_r($user_id);
//        die();
        $concert = Concert::where('concerts.user_id', $user_id)
            ->leftJoin('concert_requests', 'concert_requests.concert_id', '=', 'concerts.id')
            ->leftJoin('space_requests', 'space_requests.concert_id', '=', 'concerts.id')
            ->select('concerts.id', 'concerts.name',
                DB::raw('COALESCE(count(case when concert_requests.band_status= "rejected" then 1 else null end)) as bands_rejected_requests'),
                DB::raw('COALESCE(count(case when concert_requests.band_status = "accepted" then 1 else null end)) as bands_accepted_requests'),
                DB::raw('COALESCE(count(case when concert_requests.band_status = "pending" then 1 else null end)) as bands_pending_requests'),
                DB::raw('COALESCE(count(case when space_requests.space_status = "rejected" then 1 else null end)) as spaces_rejected_requests'),
                DB::raw('COALESCE(count(case when space_requests.space_status = "accepted" then 1 else null end)) as spaces_accepted_requests'),
                DB::raw('COALESCE(count(case when space_requests.space_status = "pending" then 1 else null end)) as spaces_pending_requests'))
            ->groupBy('concerts.id', 'concerts.name')
            ->get();
        if (!empty($concert)) {
            return response()->json($concert, 200);
        }
        return response()->json('No bands found', 404);
    }


    public function showUserUpcomingConcerts(Request $request, User $user)
    {
        $users_concerts = Ticket::where('user_id', $user->id)->distinct()->pluck('concert_id')->toArray();
        $concerts = Concert::with('band', 'space', 'user')
            ->whereIn('id', $users_concerts)
            ->where('concert_start', '>', Carbon::now()->toDateString())
            ->orderBy('concert_start', 'desc')
            ->paginate();


        if (count($concerts) < 1) {
            return response()->json('No concerts found', 404);
        }
        return response()->json($concerts, 200);
    }

    public function showUserPastConcerts(Request $request, User $user)
    {
        $users_concerts = Ticket::where('user_id', $user->id)->distinct()->pluck('concert_id')->toArray();
        $concerts = Concert::with('band', 'space', 'user')
            ->whereIn('id', $users_concerts)
            ->where('concert_start', '<', Carbon::now()->toDateString())
            ->orderBy('concert_start', 'desc')
            ->paginate();

        if (count($concerts) < 1) {
            return response()->json('No concerts found', 404);
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
        $concert->concert_start = isset($request->concert_start) ? Carbon::parse($request->concert_start) : '';
        $concert->short_description = isset($request->short_description) ? $request->short_description : null;
        $concert->long_description = isset($request->long_description) ? $request->long_description : null;

        if ($request->get('poster_url')) {

            $image = $request->get('poster_url');
            $filename = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('poster_url'))->resize(300, 300)->save(public_path('uploads/concert_pictures/') . $filename);
            $concert->poster_url = 'http://127.0.0.1:8000/uploads/concert_pictures/' . $filename;;
        }
        $concert->user_id = auth()->user()->id;

        $concert->save();

        return response()->json($concert, 200);
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
