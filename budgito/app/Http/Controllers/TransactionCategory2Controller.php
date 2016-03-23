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

    /**
     * get the category2 data based on selected category1;
     * if no category1 provided, return all category2 data.
     *
     * @return json of category2 data;
     */
    public function getData() {
        // check if we have a selected category1;
        if (Request::has('category1')) {
            
            $category1 = Request::get('category1');
            
            // return category2 data that corresponds to selected category1;
            return \App\TransactionCategory2::select('id', 'name')
                ->where('transaction_category1_id', '=', $category1)
                ->orderBy('name', 'asc')
                ->get()
                ->toJson();
        }
        // else no selected category1; return all category2 data;
        else {
            return \App\TransactionCategory2::select('id', 'name')
              ->orderBy('name', 'asc')
              ->get()
              ->toJson();
        }
    }
}
