<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

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


        return response($concerts);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
