<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function index()
    {
        $limit = 20;

        $countries = Country::select('id', 'name')->paginate($limit);

        return response($countries);
    }
}
