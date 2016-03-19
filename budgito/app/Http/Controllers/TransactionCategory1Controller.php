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

    public function getData() {
        if (Request::has('transaction_type')) {
            
            $transactionType = Request::get('transaction_type');
            
            return \App\TransactionCategory1::select('id', 'name')
                ->where('transaction_type_id', '=', $transactionType)
                ->orderBy('name', 'asc')
                ->get()
                ->toJson();
        }
        else {
            return \App\TransactionCategory1::select('id', 'name')
              ->orderBy('name', 'asc')
              ->get()
              ->toJson();
        }
    }
}
