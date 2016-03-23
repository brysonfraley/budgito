<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;

class TransactionCategory1Controller extends Controller
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
     * get the category1 data based on selected transaction_type;
     * if no transaction_type provided, return all category1 data.
     *
     * @return json of category1 data;
     */
    public function getData() {
        // check if we have a transaction_type
        if (Request::has('transaction_type')) {
            
            $transactionType = Request::get('transaction_type');
            
            // return category1 data that corresponds to selected
            // transaction_type
            return \App\TransactionCategory1::select('id', 'name')
                ->where('transaction_type_id', '=', $transactionType)
                ->orderBy('name', 'asc')
                ->get()
                ->toJson();
        }
        // else no transaction_type; return all category1 data;
        else {
            return \App\TransactionCategory1::select('id', 'name')
              ->orderBy('name', 'asc')
              ->get()
              ->toJson();
        }
    }
}
