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
        // decode url account name;
        $accountName = urldecode($accountNameEncoded);
        
        // get all the user's accounts to display in nav dropdown;
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        // get the date of first transaction recorded; 
        // needed for date range filter;
        $firstTransactionDate = \App\Transaction::min("date");
        
        // collect data to pass to the page view
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" => $accountNameEncoded,
            "accounts" => $accounts,
            "firstTransactionDate" => $firstTransactionDate,
            "pageTitle" => "Dashboard",
            "showHeader" => true
        ];
        
        // load the page view;
        return view('dashboard', $data);
    }
    
}
