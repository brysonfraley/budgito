<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;

class TransactionController extends Controller
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
        
        // get all the user's accounts to display in nav dropdown
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        $transactions = \Auth::user()
          ->accounts()
          ->where("name", "=", $accountName)
          ->firstOrFail()
          ->transactions()
          ->with(["transactionCategory2" => function ($query) {
              $query->select("id", "name", "transaction_category1_id")
                ->with(["transactionCategory1" => function ($query) {
                  $query->select("id", "name", "transaction_type_id")
                    ->with(["transactionType" => function ($query) {
                        $query->select("id", "name");
                    }]);
              }]);
          }])
          ->orderBy("date", "desc")
          ->take(50)
          ->get()
          ->toArray();
        
        //dd($transactions[0]);
        
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "accounts" => $accounts,
            "pageTitle" => "Transactions",
            "showHeader" => true,
            "transactions" => $transactions
        ];
        return view('transactions', $data);
    }
    
    public function add($accountNameEncoded)
    {
        $accountName = urldecode($accountNameEncoded);
        
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        $transactionTypes = \App\TransactionType::orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "pageTitle" => "Add Transaction",
            "showHeader" => true,
            "accounts" => $accounts,
            "transactionTypes" => $transactionTypes
        ];
        return view('transactions_add', $data);
    }
    
    public function store($accountNameEncoded) {
        $accountName = urldecode($accountNameEncoded);
        
        $input = Request::all();
        
        $input["transaction_category2_id"] = $input["category2"];
        
        \Auth::user()
          ->accounts()
          ->where("name", "=", $accountName)
          ->firstOrFail()
          ->transactions()
          ->create($input);
        
        // Send a flash message of success to the view.
        session()->flash('flash_message', 'Your new transaction has been added!');
        
        // Redirect to transactions page.
        return redirect($accountNameEncoded . "/transactions");
        
    }
    
    public function getData() {
        $startDate = Request::get("startDate");
        $endDate = Request::get("endDate");
        $accountName = urldecode(Request::get("accountName"));
        
        $accountId = \Auth::user()
          ->accounts()
          ->select("id")
          ->where("name", "=", $accountName)
          ->firstOrFail();
        $accountId = $accountId["id"];
        
        $transactions = \DB::table("transactions")
          ->join("transaction_category2s", 
            "transactions.transaction_category2_id", "=",
            "transaction_category2s.id")
          ->join("transaction_category1s", 
            "transaction_category2s.transaction_category1_id", "=", 
            "transaction_category1s.id")
          ->select("transaction_category1s.name", 
            \DB::raw("count(*) as transactions_count"), 
            \DB::raw("sum(transactions.amount) as amount_total"))
          ->where([
            ["transactions.account_id", "=", $accountId],
            ["transactions.date", ">=", $startDate],
            ["transactions.date", "<=", $endDate]])
          ->groupBy("transaction_category1s.name")
          ->orderBy("amount_total", "desc")
          ->get();
        
        return json_encode($transactions);
    }
}
