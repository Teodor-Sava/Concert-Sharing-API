<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Concert $concert)
    {
        $reviews = Review::with('user')
            ->where('concert_id', $concert->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reviews, 201);
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
    public function store(Request $request, Concert $concert)
    {
        if ($concert->start < Carbon::now()) {
            $review = new Review();
            $review->title = $request->title;
            $review->message = $request->message;
            $review->concert_id = $concert->id;
            $review->user_id = auth()->user()->id;
            $review->save();

            $response = ['message' => 'A reviews has been added'];
            return response()->json($response, 201);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Review $review
     * @return \Illuminate\Http\Response
     */
    public function show(Concert $concert, Review $review)
    {
        return response()->json($review, 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Review $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Review $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Concert $concert, Review $review)
    {
        if ($review->user_id === auth()->user()->id) {
            $review->title = $request->title;
            $review->message = $request->message;
            $review->save();

            return response()->json($review, 201);
        }
        return response()->json("You are not authorized for this action", 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review $review
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Concert $concert, Review $review)
    {
        if ($review->user_id === auth()->user()->id) {
            $review->delete();
            return response()->json('Review deleted', 201);
        }
        return response()->json('You are not authorized for this action', 401);

    }
}
