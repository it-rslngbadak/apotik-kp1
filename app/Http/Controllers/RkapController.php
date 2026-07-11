<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RkapController extends Controller
{
    public function index()
    {
        return view('rkap.index');
    }

    public function getUnitsData() {}
}
