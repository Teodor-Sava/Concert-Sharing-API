<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDetailsResource;
use App\User;
use App\UserDetails;
use Illuminate\Http\Request;

class UserDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $user_details = UserDetails::where('user_id', auth()->user()->id);
        if (empty($user_details)) {
            $user_details = new UserDetails();
            $user_details->dob = $request->dob;
            $user_details->description = $request->description;
            $user_details->country_id = $request->country_id;
            $user_details->user_id = auth()->user()->id;

            $user_details->save();

            return response()->json('User details created', 201);
        }
        return response()->json('User details already exist', 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserDetails $userDetails
     * @return \Illuminate\Http\Response
     */
    public function getUserDetailsByUserID(User $user)
    {
        $user_details = UserDetails::where('user_id', $user->id)->get();

        if (count($user_details) > 0) {
            return response(new UserDetailsResource($user_details[0]));
        }
        return response()->json('User details not found', 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserDetails $userDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(UserDetails $userDetails)
    {

    }

    public function getLoggedInUserData()
    {
        $user_details = UserDetails::with()->where('user_id', auth()->user()->id)->get();

        if (count($user_details) > 0) {
            return response(new UserDetailsResource($user_details[0]));
        }
        return response()->json('User details not found', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\UserDetails $userDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $userDetails = UserDetails::where('user_id', $user->id)->first();
        if ($user->id === auth()->user()->id) {
            $userDetails->update($request->all());
            return response()->json('Details have been modified', 201);
        }
        return response()->json('You are not authorized for this action', 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserDetails $userDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDetails $userDetails)
    {
        //
    }
}
