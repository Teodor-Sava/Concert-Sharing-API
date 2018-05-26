<?php

namespace App\Http\Controllers;

use App\Genre;
use Illuminate\Http\Request;

class GenresController extends Controller
{
    public function index()
    {
        $limit = 20;

        $genres = Genre::select('id', 'type')->paginate($limit);

        return response($genres);
    }
}
