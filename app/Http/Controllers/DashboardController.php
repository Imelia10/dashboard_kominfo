<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function economy()
    {
        return view('pages.economy');
    }

    public function demographics()
    {
        return view('pages.demographics');
    }

    public function health()
    {
        return view('pages.health');
    }

    public function environment()
    {
        return view('pages.environment');
    }

    public function education()
    {
        return view('pages.education');
    }
}
