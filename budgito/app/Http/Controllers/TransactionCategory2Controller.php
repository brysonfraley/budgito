<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;

class TransactionCategory2Controller extends Controller
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
        if (Request::has('category1')) {
            
            $category1 = Request::get('category1');
            
            return \App\TransactionCategory2::select('id', 'name')
                ->where('transaction_category1_id', '=', $category1)
                ->orderBy('name', 'asc')
                ->get()
                ->toJson();
        }
        else {
            return \App\TransactionCategory2::select('id', 'name')
              ->orderBy('name', 'asc')
              ->get()
              ->toJson();
        }
    }
}
