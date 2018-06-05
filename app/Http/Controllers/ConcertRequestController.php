<?php

namespace App\Http\Controllers;

use App\Concert;
use App\ConcertRequest;
use Illuminate\Http\Request;

class ConcertRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $concerts = Concert::where('user_id', auth()->user()->id);
        if (!empty($concert) && $concert->id === $concertRequest->concert_id) {
            if ($request->status === true) {
                $concertRequest->status = 'accepted';
            } else {
                $concertRequest->status = 'rejected';
            }
            $concertRequest->save();
            return response()->json('Request has been updated', 200);
        }
        return response()->json('User not allowed to modify the request', 404);
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
//        print_r($request->all());
//        die();
        $crequest = ConcertRequest::firstOrCreate(['user_id' => $request->user_id, 'band_id' => $request->band_id, 'concert_id' => $request->concert_id],
            ['request_message' => $request->request_message, 'status' => 'pending']);
        print_r($crequest);
        die();
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
        $concert = Concert::where('id', $concertRequest->concert_id)->firstOrFail();
        if (!empty($concert) && $concert->user_id === auth()->user()->id) {
            if ($request->status === true) {
                $concertRequest->status = 'accepted';
                $concert->band_id = $concertRequest->band_id;
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
