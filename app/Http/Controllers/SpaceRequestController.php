<?php

namespace App\Http\Controllers;

use App\Band;
use App\Concert;
use App\ConcertRequest;
use App\Space;
use App\SpaceRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaceRequestController extends Controller
{
    public function getRequestForSpaceAdmin(Space $space)
    {
        if ($space->user_id === auth()->user()->id) {
            $space_request_pending = SpaceRequest::where('space_id', $space->id)
                ->where('space_status', 'pending')
                ->with('concert', 'user')
                ->orderBy('created_at', 'desc')
                ->get();
            $space_request_accepted = SpaceRequest::where('space_id', $space->id)
                ->where('space_status', 'accepted')
                ->with('concert', 'user')
                ->orderBy('created_at', 'desc')
                ->get();

            $space_request_rejected = SpaceRequest::where('space_id', $space->id)
                ->where('space_status', 'rejected')
                ->with('concert', 'user')
                ->orderBy('created_at', 'desc')
                ->get();
            $data = [
                'pending_requests' => count($space_request_pending) > 0 ? $space_request_pending : false,
                'accepted_requests' => count($space_request_accepted) > 0 ? $space_request_accepted : false,
                'rejected_requests' => count($space_request_rejected) > 0 ? $space_request_rejected : false
            ];
            return response()->json($data, 200);
        }

        return response()->json('This user is not the administrator of any spaces', 404);
    }

    public function getAcceptedSpaceRequestsForConcertAdmin(Concert $concert)
    {
        if (!empty($concert->concert_public)) {
            return response()->json('Concert is already public', 401);
        }
        if ($concert->user_id === auth()->user()->id) {
            $spaceRequest = SpaceRequest::with('space')
                ->where('concert_id', $concert->id)
                ->where('space_status', 'accepted')
                ->orderBy('updated_at', 'desc')
                ->get();
            return response()->json($spaceRequest, 200);
        }

        return response()->json('This user is not the administrator of the concert', 404);
    }

    public function getAllRequestsForSpacesAdmin()
    {
        $user_id = auth()->user()->id;
        $spaces = Space::where('spaces.user_id', $user_id)
            ->leftJoin('space_requests', 'space_requests.space_id', '=', 'spaces.id')
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
        $user = auth()->user();

        $concert = Concert::find($request->concert_id)->where('user_id', $user->id)->get();

        if (!empty($concert)) {
            SpaceRequest::firstOrCreate(['user_id' => $user->id, 'space_id' => $request->space_id, 'concert_id' => $request->concert_id],
                ['request_message' => $request->request_message, 'status' => 'pending']);

            return response()->json('A request has been sent', 200);
        }

        return response()->json('Something went wrong', 409);
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
     * @param SpaceRequest $spaceRequest
     * @param Space $space
     * @return \Illuminate\Http\Response
     */


    public function confirmSpaceForConcert(Request $request, SpaceRequest $spaceRequest)
    {
        $message = '';
        $concert = Concert::find($spaceRequest->concert_id);
//        print_r($spaceRequest->concert_id);
//        die();
        if ($concert->user_id === auth()->user()->id) {
            if ($spaceRequest->space_status === 'accepted') {
                $spaceRequest->concert_status = 'accepted';
                $current_concert_requests = SpaceRequest::all()->where('concert_id', $concert->id)->except($spaceRequest->id);
                foreach ($current_concert_requests as $crequest) {
                    $crequest->concert_status = 'rejected';
                    $crequest->save();
                }
                $concert->space_id = $spaceRequest->space_id;
                if (isset($concert->band_id)) {
                    $concert->concert_public = true;
                    $message = "Concert has been made public";
                } else {
                    $message = 'Band is now playing at the concert';
                }
                $spaceRequest->save();
                $concert->save();
                $space = Space::find($concert->space_id);
                $data = [
                    'message' => $message,
                    'space' => $space,
                    'concert' => $concert
                ];
                return response()->json($data, 200);
            } else {
                $message = 'Band did not accept to play at the concert';
                return response()->json($message, 401);
            }
        }
        $message = 'You are not authorized to complete this request';

        return response()->json($message, 401);
    }

    public function acceptSpaceRequestBySpaceAdmin(SpaceRequest $spaceRequest, Space $space)
    {
        if (!empty($space) && $space->user_id === auth()->user()->id) {
            $spaceRequest->space_status = 'accepted';
            $spaceRequest->save();
            return response()->json('Request has been updated', 200);
        }
        return response()->json('User not allowed to modify the request', 404);
    }

    public function declineSpaceRequestBySpaceAdmin(SpaceRequest $spaceRequest)
    {
        if (!empty($band) && $band->user_id === auth()->user()->id) {
            $spaceRequest->space_status = 'rejected';
            $spaceRequest->save();
            return response()->json('Request has been updated', 200);
        }
        return response()->json('User not allowed to modify the request', 404);
    }


    public function destroy($id)
    {
        //
    }
}
