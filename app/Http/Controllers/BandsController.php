<?php

namespace App\Http\Controllers;

use App\Band;
use Illuminate\Http\Request;

class BandsController extends Controller
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

        if (isset($queryParams['limit'])) {
            $limit = $queryParams['limit'];
        }

        if (isset($queryParams['search'])) {
            $searchParams = $queryParams['search'];
            $limit = 10;
            $bands = Band::with('genre', 'country')
                ->where('name', 'LIKE', "%{$searchParams}%")
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        } else {
            $bands = Band::with('genre', 'country')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }


        return response($bands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!empty($request->all)) {
            return Band::create($request->all);
        }
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
