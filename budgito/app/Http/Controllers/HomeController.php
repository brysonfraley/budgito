<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // collect data to pass to the page view
        $data = [
            "pageTitle" => "Home",
            "showHeader" => true
        ];
        
        // load the page view;
        return view('home', $data);
    }
}
