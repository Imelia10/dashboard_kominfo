<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    

    public function demographics()
    {
        return view('pages.demographics');
    }


    public function environment()
    {
        return view('pages.environment');
    }

}

