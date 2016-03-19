<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($accountNameEncoded)
    {
        $accountName = urldecode($accountNameEncoded);
        
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" => $accountNameEncoded,
            "accounts" => $accounts,
            "pageTitle" => "Dashboard",
            "showHeader" => true
        ];
        return view('dashboard', $data);
    }
    
}
