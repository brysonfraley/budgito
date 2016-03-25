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

    /** Show the transactions page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($accountNameEncoded)
    {
        // decode url account name;
        $accountName = urldecode($accountNameEncoded);
        
        // get all the user's accounts to display in nav dropdown
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        // get the user's transactions for the selected account;
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
          ->get()
          ->toArray();
        
        // collect data to pass to the page view
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "accounts" => $accounts,
            "pageTitle" => "Transactions",
            "showHeader" => true,
            "transactions" => $transactions
        ];
        
        // load the page view;
        return view('transactions', $data);
    }
    
    /** Show the add transaction page.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($accountNameEncoded)
    {
        // decode url account name;
        $accountName = urldecode($accountNameEncoded);
        
        // get all the user's accounts to display in nav dropdown
        $accounts =  \Auth::user()
          ->accounts()
          ->select('id', 'name')
          ->orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        // get transaction types for dropdown select;
        $transactionTypes = \App\TransactionType::orderBy('name', 'asc')
          ->get()
          ->toArray();
        
        // collect data to pass to the page view;
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "pageTitle" => "Add Transaction",
            "showHeader" => true,
            "accounts" => $accounts,
            "transactionTypes" => $transactionTypes
        ];
        
        // load the page view;
        return view('transactions_add', $data);
    }
    
    /** store a new transaction.
     *
     * @return redirect to transaction page;
     */
    public function store($accountNameEncoded) {
        // decode url account name;
        $accountName = urldecode($accountNameEncoded);
        
        // get all transaction input values;
        $input = Request::all();
        
        // update the input name of category2_id to match name in db;
        $input["transaction_category2_id"] = $input["category2"];
        
        // create new transaction with input values;
        \Auth::user()
          ->accounts()
          ->where("name", "=", $accountName)
          ->firstOrFail()
          ->transactions()
          ->create($input);
        
        // Send a flash message of success to the view.
        session()->flash('flash_message', 
          'Your new transaction has been added!');
        
        // Redirect to transactions page.
        return redirect($accountNameEncoded . "/transactions");
    }
    
    /** get transaction data based on selected date/time frame;
     *
     * @return json of transaction data;
     */
    public function getData() {
        // get the selected start date and end date to query transactions;
        //$startDate = Request::get("startDate");
        //$endDate = Request::get("endDate");
        $startDate = "2016-03-01";
        $endDate = "2016-03-31";
        
        // decode url account name;
        //$accountName = urldecode(Request::get("accountName"));
        $accountName = "Chase Checking";
        
        // get the account id for looking up its transactions;
        $accountId = \Auth::user()
          ->accounts()
          ->select("id")
          ->where("name", "=", $accountName)
          ->firstOrFail();
        $accountId = $accountId["id"];
        
        // get the transaction data for the account, within the time frame;
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
        
        
        // get all category1s and category2s from db;
        $allCats = \App\TransactionType::select("id", "name")
          //->where("name", "=", "expense")
          ->with(["transactionCategory1s" => function ($query) {
              $query->select("id", "name", "transaction_type_id")
                ->with(["transactionCategory2s" => function ($query) {
                    $query->select("id", "name", "transaction_category1_id");
                }]);
          }])
          ->get()
          ->toArray();
          
        // update $all categories array with key/values for 
        // number of transaction counters and transaction amount sums;
        for($i=0; $i<count($allCats); $i++) {
            $allCats[$i]["number_of_transactions"] = 0;
            $allCats[$i]["transaction_amount"] = 0;
            for($j=0; $j<count($allCats[$i]["transaction_category1s"]); $j++) {
                $allCats[$i]["transaction_category1s"][$j]
                  ["number_of_transactions"] = 0;
                $allCats[$i]["transaction_category1s"][$j]
                  ["transaction_amount"] = 0;
                for($k=0; $k<count($allCats[$i]["transaction_category1s"][$j]
                  ["transaction_category2s"]); $k++) {
                      $allCats[$i]["transaction_category1s"][$j]
                        ["transaction_category2s"][$k]
                        ["number_of_transactions"] = 0;
                      $allCats[$i]["transaction_category1s"][$j]
                        ["transaction_category2s"][$k]
                        ["transaction_amount"] = 0;
                }
            }
        }
        
        // get all transactions data within the selected time frame;
        $transactions = \DB::table("transactions")
          ->join("transaction_category2s", 
            "transactions.transaction_category2_id", "=",
            "transaction_category2s.id")
          ->join("transaction_category1s", 
            "transaction_category2s.transaction_category1_id", "=", 
            "transaction_category1s.id")
          ->join("transaction_types", 
            "transaction_category1s.transaction_type_id", "=", 
            "transaction_types.id")
          ->select("transaction_types.id as transaction_type_id",
            "transaction_category1s.id as transaction_category1_id",
            "transactions.transaction_category2_id",
            "transactions.amount")
          ->where([
            ["transactions.account_id", "=", $accountId],
            ["transactions.date", ">=", $startDate],
            ["transactions.date", "<=", $endDate]])
          ->orderBy("transaction_type_id", "asc")
          ->orderBy("transaction_category1_id", "asc")
          ->orderBy("transaction_category2_id", "asc")
          ->get();
        
        dd($transactions);
        
        foreach ($transactions as $tran) {
            
        }
        
        // set up a new array of all categories and transactions with 
        // number of transaction counts and transaction amount sums;
        // in the format of:
        // category_type > category1s > category2s > transactions;
        $categoryTransactions = [];
        foreach($allCategories as $transactionType) {
            $newCategory1s = [];
            foreach($transactionType["transaction_category1s"] as $category1) {
                $newCategory2s = [];
                $cat1NumOfTrans = 0;
                $cat1Amount = 0;
                foreach($category1["transaction_category2s"] as $category2) {
                    $category2Id = $category2["id"];
                    $cat2NumOfTrans = 0;
                    $cat2Amount = 0;
                    if (isset($budgetLookUp[$category2Id])) {
                        $budgetAmount = $budgetLookUp[$category2Id];
                        $category1TotalAmount += $budgetAmount;
                    }
                    $category2["budget_amount"] = $budgetAmount;
                    array_push($newCategory2s, $category2);
                }
                $category1["transaction_category2s"] = $newCategory2s;
                $category1["budget_amount"] = $category1TotalAmount;
                array_push($newCategory1s, $category1);
            }
            $transactionType["transaction_category1s"] = $newCategory1s;
            array_push($categoryBudgets, $transactionType);
        }
        
        // return json of transaction data;
        return json_encode($transactions);
    }
}
