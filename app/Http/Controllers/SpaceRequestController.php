<?php

namespace App\Http\Controllers;

use App\Space;
use App\SpaceRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaceRequestController extends Controller
{
    public function showPendingRequestForSpaceAdmin(Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space_request = SpaceRequest::where('space_id', $space->id)->where('status', 'pending')->get();
            return response()->json($space_request, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function showAcceptedRequestsForBandsAdmin(Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space_request = SpaceRequest::where('space_id', $space->id)->where('status', 'accepted')->get();
            return response()->json($space_request, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function showRejectedRequestsForBandsAdmin(Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space_request = SpaceRequest::where('space_id', $space->id)->where('status', 'rejected')->get();
            return response()->json($space_request, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function showRequestsForSpaceAdmin(Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space_request = SpaceRequest::where('space_id', $space->id)->get();
            return response()->json($space_request, 200);
        }

        return response()->json('This user is not the administrator of the concert', 404);
    }

    public function showAllRequestsOfAnUser()
    {
        $user = User::find(auth()->user()->id);

        return response()->json($user->spaceRequests());
    }

    public function getAllRequestsForSpacesAdmin()
    {
        $user_id = auth()->user()->id;
        $spaces = Space::where('spaces.user_id', $user_id)
            ->join('space_requests', 'space_requests.space_id', '=', 'spaces.id')
            ->select('spaces.id', 'spaces.name',
                DB::raw('count(case when space_requests.space_status= "rejected" then 1 else null end) as rejected_requests'),
                DB::raw('count(case when space_requests.space_status = "accepted" then 1 else null end) as accepted_requests'),
                DB::raw('count(case when space_requests.space_status = "pending" then 1 else null end) as pending_requests'))
            ->groupBy('spaces.id', 'spaces.name')
            ->get();

        if ($spaces) {
            return response()->json($spaces, 200);
        }
        return response()->json('No Spaces found', 404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
        $concert = Concert::find($request->concert_id);

        $crequest = ConcertRequest::firstOrCreate(['user_id' => $request->user_id, 'band_id' => $request->band_id, 'concert_id' => $request->concert_id],
            ['request_message' => $request->request_message, 'status' => 'pending']);

        return response()->json('A request has been sent', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ConcertRequest $concertRequest)
    {
        return response()->json($concertRequest, 200);
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
    public function update(Request $request, ConcertRequest $concertRequest)
    {
        $band = Band::where('id', $concertRequest->band_id)->firstOrFail();
        if (!empty($band) && $band->user_id === auth()->user()->id) {
            if ($request->status === true) {
                $concert = Concert::where('id', $concertRequest->concert_id);
                $concertRequest->status = 'accepted';
                if (isset($concert->space_id)) {
                    $concert->concert_public = true;
                }
                $concert->band_id = $band->band_id;
                $concert->save();
            } else {
                $concertRequest->status = 'rejected';
            }
            $concertRequest->save();
            return response()->json('Request has been updated', 200);
        }
        return response()->json('User not allowed to modify the request', 404);
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
