<?php

namespace App\Http\Controllers;

use App\Band;
use App\Concert;
use App\ConcertRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConcertRequestController extends Controller
{
    public function getPendingRequestForBandsAdmin(Band $band)
    {
        if ($band->user_id === auth()->user()->id) {
            $concertRequest = ConcertRequest::where('band_id', $band->id)->where('band_status', 'pending')->with('concert', 'band', 'user')->get();
            return response()->json($concertRequest, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function getAcceptedRequestsForBandsAdmin(Band $band)
    {
        if ($band->user_id === auth()->user()->id) {
            $concertRequest = ConcertRequest::where('band_id', $band->id)->where('band_status', 'accepted')->get();
            return response()->json($concertRequest, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function getRejectedRequestsForBandsAdmin(Band $band)
    {
        if ($band->user_id === auth()->user()->id) {
            $concertRequest = ConcertRequest::where('band_id', $band->id)->where('band_status', 'rejected')->get();
            return response()->json($concertRequest, 200);
        }
        return response()->json('This user is not the administrator of any bands', 404);
    }

    public function getRequestsForConcertAdmin(Concert $concert)
    {
        if ($concert->user_id === auth()->user()->id) {
            $concertRequest = ConcertRequest::where('concert_id', $concert->id)->get();
            return response()->json($concertRequest, 200);
        }

        return response()->json('This user is not the administrator of the concert', 404);
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

        $concert = Concert::where('id', $request->concert_id)->where('user_id', $user->id);

        if (!empty($concert)) {
            ConcertRequest::firstOrCreate(['user_id' => $user->id, 'band_id' => $request->band_id, 'concert_id' => $request->concert_id],
                ['request_message' => $request->request_message, 'band_status' => 'pending', 'concert_status' => 'pending']);

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function confirmBandForConcert(Request $request, ConcertRequest $concertRequest)
    {
        $message = '';
        $concert = Concert::find($concertRequest->concert_id);
        if ($concert->user_id === $concertRequest->user_id) {
            if ($concertRequest->band_status === 'accepted') {
                $concertRequest->concert_status = 'accepted';
                $current_concert_requests = ConcertRequest::where('concert_id', $concert->id)->where('id', '!=', $concertRequest->id);
                foreach ($current_concert_requests as $crequest) {
                    $crequest->concert_status = 'rejected';
                    $crequest->save();
                }
                $concert->band_id = $concertRequest->band_id;
                if (isset($concert->space_id)) {
                    $concert->concert_public = true;
                    $message = "Concert has been made public";
                } else {
                    $message = 'Band is now playing at the concert';
                }
                $concertRequest->save();
                $concert->save();
                return response()->json($message, 200);
            } else {
                $message = 'Band did not accept to play at the concert';
                return response()->json($message, 401);
            }
        }
        $message = 'You are not authorized to complete this request';
        return response()->json($message, 401);
    }

//    public function changeBandStatusForConcert(Request $request, ConcertRequest $concertRequest)
//    {
//        $band = Band::where('id', $concertRequest->band_id)->firstOrFail();
//        if (!empty($band) && $band->user_id === auth()->user()->id) {
//            if ($request->status === true) {
//                $concertRequest->status = 'accepted';
//            } else {
//                $concertRequest->status = 'rejected';
//            }
//            $concertRequest->save();
//            return response()->json('Request has been updated', 200);
//        }
//        return response()->json('User not allowed to modify the request', 404);
//    }

    public function acceptConcertRequestByBand(ConcertRequest $concertRequest, Band $band)
    {
        if (!empty($band) && $band->user_id === auth()->user()->id) {
            $concertRequest->band_status = 'accepted';
            $concertRequest->save();
            return response()->json('Request has been updated', 200);
        }
        return response()->json('User not allowed to modify the request', 404);
    }

    public function declineConcertRequestByBand(ConcertRequest $concertRequest)
    {
        if (!empty($band) && $band->user_id === auth()->user()->id) {
            $concertRequest->band_status = 'rejected';
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
    public function removeConcertRequest(ConcertRequest $concertRequest)
    {

    }
}
