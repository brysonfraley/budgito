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
        
//        // get the transaction data for the account, within the time frame;
//        $transactions = \DB::table("transactions")
//          ->join("transaction_category2s", 
//            "transactions.transaction_category2_id", "=",
//            "transaction_category2s.id")
//          ->join("transaction_category1s", 
//            "transaction_category2s.transaction_category1_id", "=", 
//            "transaction_category1s.id")
//          ->select("transaction_category1s.name", 
//            \DB::raw("count(*) as transactions_count"), 
//            \DB::raw("sum(transactions.amount) as amount_total"))
//          ->where([
//            ["transactions.account_id", "=", $accountId],
//            ["transactions.date", ">=", $startDate],
//            ["transactions.date", "<=", $endDate]])
//          ->groupBy("transaction_category1s.name")
//          ->orderBy("amount_total", "desc")
//          ->get();
//          
        // get all transactions and budget data for the selected account and 
        // time frame;
        $transAndBdgts = \App\TransactionType::select("id", "name")
          ->with(["transactionCategory1s" => function ($query) use ($accountId, $startDate, $endDate) {
              $query->select("id", "name", "transaction_type_id")
                ->with(["transactionCategory2s" => function ($query) use ($accountId, $startDate, $endDate) {
                    $query->select("id", "name", "transaction_category1_id")
                      ->with(["transactions" => function ($query) use ($accountId, $startDate, $endDate) {
                        $query->select(\DB::raw("count(*) as number_of_transactions"), 
                          \DB::raw("sum(amount) as amount"), "transaction_category2_id")
                          ->where([["account_id", "=", $accountId],
                            ["date", ">=", $startDate],
                            ["date", "<=", $endDate]
                          ])
                          ->groupBy("transaction_category2_id");
                      },
                      "budgets" => function ($query) use ($accountId) {
                        $query->select("amount", "transaction_category2_id")
                          ->where("account_id", "=", $accountId);
                      }]);
                }]);
          }])
          ->get()
          ->toArray();
          
          $transAndBdgtsSubset = [];
          foreach ($transAndBdgts as $transType) {
              $transTypeNumOfTrans = 0;
              $transTypeTransAmount = 0;
              $transTypeBdgtAmount = 0;
              $cat1Subset = [];
              
              foreach ($transType["transaction_category1s"] as $transCat1) {
                  $cat1NumOfTrans = 0;
                  $cat1TransAmount = 0;
                  $cat1BdgtAmount = 0;
                  $cat2Subset = [];
                  
                  foreach ($transCat1["transaction_category2s"] as $transCat2) {
                      // check if we have both transactions and budget data;
                      if (!empty($transCat2["transactions"]) && !empty($transCat2["budgets"])) {
                          /* add both transaction and budget data to subset; */
                          
                          // variables for transaction and budget data;
                          $cat2NumOfTrans = $transCat2["transactions"][0]["number_of_transactions"];
                          $cat2TransAmount = $transCat2["transactions"][0]["amount"];
                          $cat2BdgtAmount = $transCat2["budgets"][0]["amount"];
                          
                          // set up new array for cat2 data and store cat2 
                          // subset;
                          $newCat2 = [
                            "name" => $transCat2["name"],
                            "number_of_transactions" => $cat2NumOfTrans,
                            "transaction_amount" => $cat2TransAmount,
                            "budget_amount" => $cat2BdgtAmount
                          ];
                          $cat2Subset[$transCat2["id"]] = $newCat2;
                          
                          // update category1 counts and amounts;
                          $cat1NumOfTrans += $cat2NumOfTrans;
                          $cat1TransAmount += $cat2TransAmount;
                          $cat1BdgtAmount += $cat2BdgtAmount;
                      }
                      // or check if we only have transaction data;
                      elseif (!empty($transCat2["transactions"])) {
                          /* add just transaction data to subset; */
                          
                          // variables for transaction;
                          $cat2NumOfTrans = $transCat2["transactions"][0]["number_of_transactions"];
                          $cat2TransAmount = $transCat2["transactions"][0]["amount"];
                          
                          // set up new array for cat2 data and store cat2 
                          // subset;
                          $newCat2 = [
                            "name" => $transCat2["name"],
                            "number_of_transactions" => $cat2NumOfTrans,
                            "transaction_amount" => $cat2TransAmount,
                            "budget_amount" => 0
                          ];
                          $cat2Subset[$transCat2["id"]] = $newCat2;
                          
                          // update category1 counts and amounts;
                          $cat1NumOfTrans += $cat2NumOfTrans;
                          $cat1TransAmount += $cat2TransAmount;
                      }
                      // or check if we only have budget data;
                      elseif (!empty($transCat2["budgets"])) {
                          /* add just budget data to subset; */
                          
                          // variables for budget data;
                          $cat2BdgtAmount = $transCat2["budgets"][0]["amount"];
                          
                          // set up new array for cat2 data and store cat2 
                          // subset;
                          $newCat2 = [
                            "name" => $transCat2["name"],
                            "number_of_transactions" => 0,
                            "transaction_amount" => 0,
                            "budget_amount" => $cat2BudgetAmount
                          ];
                          $cat2Subset[$transCat2["id"]] = $newCat2;
                          
                          // update category1 counts and amounts;
                          $cat1BdgtAmount += $cat2BdgtAmount;
                      }
                      // else no budget or transaction data; skip/exclude
                  }

                  if (!empty($cat2Subset)) {
                    // set up new array for cat1 data and store cat1 subset;
                    $newCat1 = [
                        "name" => $transCat1["name"],
                        "number_of_transactions" => $cat1NumOfTrans,
                        "transaction_amount" => $cat1TransAmount,
                        "budget_amount" => $cat1BdgtAmount,
                        "transaction_category2s" => $cat2Subset
                    ];
                    $cat1Subset[$transCat1["id"]] = $newCat1;
                    
                    // update trans type counts and amounts;
                    $transTypeNumOfTrans += $cat1NumOfTrans;
                    $transTypeTransAmount += $cat1TransAmount;
                    $transTypeBdgtAmount += $cat1BdgtAmount;
                  }
              }
              
              //if (!empty($cat1Subset)) {
                // set up new array for trans type data and store trans type
                //subset;
                $newTransType = [
                    "name" => $transType["name"],
                    "number_of_transactions" => $transTypeNumOfTrans,
                    "transaction_amount" => $transTypeTransAmount,
                    "budget_amount" => $transTypeBdgtAmount,
                    "transaction_category1s" => $cat1Subset
                ];
                $transAndBdgtsSubset[$transType["id"]] = $newTransType;
              //}
          }
          dd($transAndBdgtsSubset);
        
//        // get all transactions data within the selected time frame;
//        $transactions = \DB::table("transactions")
//          ->join("transaction_category2s", 
//            "transactions.transaction_category2_id", "=",
//            "transaction_category2s.id")
//          ->join("transaction_category1s", 
//            "transaction_category2s.transaction_category1_id", "=", 
//            "transaction_category1s.id")
//          ->join("transaction_types", 
//            "transaction_category1s.transaction_type_id", "=", 
//            "transaction_types.id")
//          ->select("transaction_types.id as transaction_type_id",
//            "transaction_types.name as transaction_type_name",
//            "transaction_category1s.id as transaction_category1_id",
//            "transaction_category1s.name as transaction_category1_name",
//            "transactions.transaction_category2_id",
//            "transaction_category2s.name as transaction_category2_name",
//            "transactions.amount")
//          ->where([
//            ["transactions.account_id", "=", $accountId],
//            ["transactions.date", ">=", $startDate],
//            ["transactions.date", "<=", $endDate]])
//          ->orderBy("transaction_type_id", "asc")
//          ->orderBy("transaction_category1_id", "asc")
//          ->orderBy("transaction_category2_id", "asc")
//          ->get();
//
//        $transLookUp = [
//          "number_of_transactions" => 0,
//          "amount" => 0,
//          "transaction_types" => []
//        ];
//        $prevTransTypeId = null;
//        $prevTransCat1Id = null;
//        $prevTransCat2Id = null;
//        foreach ($transactions as $tran) {
//            // variables for current transaction info;
//            $currTransTypeId = $tran["transaction_type_id"];
//            $currTransCat1Id = $tran["transaction_category1_id"];
//            $currTransCat2Id = $tran["transaction_category2_id"];
//            $currTransAmount = $tran["amount"];
//            
//            // new transaction; add current counts and amounts to trans totals
//            // in lookup array;
//            $transLookUp["number_of_transactions"] += 1;
//            $transLookUp["amount"] += $currTransAmount;
//            
//            // if we still have the same trans type id as previous, continue
//            // down the array tree and inspect category1 info;
//            if ($prevTransTypeId === $currTransTypeId) {
//                // same trans type as prevoius; we can add current counts and
//                // amounts to trans type in lookup array;
//                $transLookUp["transaction_types"][$currTransTypeId]
//                    ["number_of_transactions"] += 1;
//                $transLookUp["transaction_types"][$currTransTypeId]
//                    ["amount"] += $currTransAmount;
//                
//                // if we have the same trans cat1 id as previous, continue
//                // down the array tree and inspect category2 info;
//                if ($prevTransCat1Id === $currTransCat1Id) {
//                    // same trans cat1 as prevoius; we can add current counts
//                    // and amounts to cat1 in lookup array;
//                    $transLookUp["transaction_types"][$currTransTypeId]
//                        ["transaction_category1s"][$currTransCat1Id]
//                        ["number_of_transactions"] += 1;
//                    $transLookUp["transaction_types"][$currTransTypeId]
//                        ["transaction_category1s"][$currTransCat1Id]
//                        ["amount"] += $currTransAmount;
//                    
//                    // if we have the same trans cat2 id as previous, then 
//                    // we have same kind of trans as previous; just update trans
//                    // counts and amount sums;
//                    if ($prevTransCat2Id === $currTransCat2Id) {
//                        // update cat2 num of trans and amounts;
//                        $transLookUp["transaction_types"][$currTransTypeId]
//                            ["transaction_category1s"][$currTransCat1Id]
//                            ["transaction_category2s"][$currTransCat2Id]
//                            ["number_of_transactions"] += 1;
//                        $transLookUp["transaction_types"][$currTransTypeId]
//                            ["transaction_category1s"][$currTransCat1Id]
//                            ["transaction_category2s"][$currTransCat2Id]
//                            ["amount"] += $currTransAmount;
//                    }
//                    
//                    // else we have a new trans cat2 to record;
//                    else {
//                        // collect cat2 info and record it to full trans array;
//                        $transCat2 = [
//                            "name" => $tran["transaction_category2_name"],
//                            "number_of_transactions" => 1,
//                            "amount" => $currTransAmount
//                        ];
//                        $transLookUp["transaction_types"][$currTransTypeId]
//                            ["transaction_category1s"][$currTransCat1Id]
//                            ["transaction_category2s"][$currTransCat2Id] = 
//                            $transCat2;
//                        
//                        // current trans cat2 id now gets stored as previous;
//                        $prevTransCat2Id = $currTransCat2Id;
//                    }
//                }
//                
//                // else we have a new trans cat1 to record; also record
//                // cat2 info;
//                else {
//                    // collect current cat2 info;
//                    $transCat2 = [
//                        $tran["transaction_category2_id"] => [
//                          "name" => $tran["transaction_category2_name"],
//                          "number_of_transactions" => 1,
//                          "amount" => $currTransAmount
//                        ]
//                    ];
//                    // collect current cat1 info with cat2;
//                    $transCat1 = [
//                        "name" => $tran["transaction_category1_name"],
//                        "number_of_transactions" => 1,
//                        "amount" => $currTransAmount,
//                        "transaction_category2s" => $transCat2
//                    ];
//                    // record cat1 and cat2 info to lookup array;
//                    $transLookUp["transaction_types"][$currTransTypeId]
//                      ["transaction_category1s"][$currTransCat1Id] = $transCat1;
//                    
//                    // current trans id's now get stored as previous;
//                    $prevTransCat1Id = $currTransCat1Id;
//                    $prevTransCat2Id = $currTransCat2Id;
//                }
//            }
//            
//            // else we have a new trans type to record; also record cat1 and
//            // cat2 info;
//            else {
//                // collect current cat2 info;
//                $transCat2 = [
//                  $currTransCat2Id => [
//                    "name" => $tran["transaction_category2_name"],
//                    "number_of_transactions" => 1,
//                    "amount" => $currTransAmount
//                  ]
//                ];
//                // collect current cat1 info with cat2;
//                $transCat1 = [
//                  $currTransCat1Id => [
//                    "name" => $tran["transaction_category1_name"],
//                    "number_of_transactions" => 1,
//                    "amount" => $currTransAmount,
//                    "transaction_category2s" => $transCat2
//                  ]
//                ];
//                // collect current trans type info with cat1 and cat2;
//                $transType = [
//                    "name" => $tran["transaction_type_name"],
//                    "number_of_transactions" => 1,
//                    "amount" => $currTransAmount,
//                    "transaction_category1s" => $transCat1
//                ];
//                // record trans type, cat1, and cat2 info to lookup array;
//                $transLookUp["transaction_types"][$currTransTypeId] = $transType;
//                
//                // current id's now gets stored as previous;
//                $prevTransTypeId = $currTransTypeId;
//                $prevTransCat1Id = $currTransCat1Id;
//                $prevTransCat2Id = $currTransCat2Id;
//            }
//        }
        
        // return json of transaction data;
        //return $transAndBdgts;
    }
}
