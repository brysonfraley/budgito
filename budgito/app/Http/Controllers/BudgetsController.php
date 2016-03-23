<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;
use DB;

class BudgetsController extends Controller
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
     * Show the dashboard page.
     * @param accountNameEncoded - selected account name from the url;
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
        
        // get all category1s and category2s from db;
        $allCategories = \App\TransactionType::select("id", "name")
          ->where("name", "=", "expense")
          ->with(["transactionCategory1s" => function ($query) {
              $query->select("id", "name", "transaction_type_id")
                ->with(["transactionCategory2s" => function ($query) {
                    $query->select("id", "name", "transaction_category1_id");
                }]);
          }])
          ->get()
          ->toArray();

        // get all budgets for the account from db;
        $budgets = \Auth::user()
          ->accounts()
          ->where("name", "=", $accountName)
          ->firstOrFail()
          ->budgets()
          ->select("transaction_category2_id", "amount")
          ->get()
          ->toArray();
        
        // create a look-up associative array of category2_id=>budget
        $budgetLookUp = [];
        foreach ($budgets as $budget) {
            $budgetLookUp[$budget["transaction_category2_id"]] = 
              ["amount" => $budget["amount"]];
        }
        
        // set up a new array of all categories and budgets;
        // in the format of:
        // category_type > category1s > category2s > category2_budgets;
        $categoryBudgets = [];
        foreach($allCategories as $transactionType) {
            $newCategory1s = [];
            foreach($transactionType["transaction_category1s"] as $category1) {
                $newCategory2s = [];
                $category1TotalAmount = 0;
                foreach($category1["transaction_category2s"] as $category2) {
                    $category2Id = $category2["id"];
                    $budgetData = [];
                    if (isset($budgetLookUp[$category2Id])) {
                        $budgetData = $budgetLookUp[$category2Id];
                        $category1TotalAmount += $budgetData["amount"];
                    }
                    $category2["budgets"] = $budgetData;
                    array_push($newCategory2s, $category2);
                }
                $category1["transaction_category2s"] = $newCategory2s;
                $category1["budget_amount"] = $category1TotalAmount;
                array_push($newCategory1s, $category1);
            }
            $transactionType["transaction_category1s"] = $newCategory1s;
            array_push($categoryBudgets, $transactionType);
        }
        
        // collect data to pass to the page view;
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "accounts" => $accounts,
            "pageTitle" => "Budgets",
            "showHeader" => true,
            "categoryBudgets" => $categoryBudgets
        ];
        
        // load the page view;
        return view('budgets', $data);
    }
    
    /**
     * Store the budget amounts.
     * @param accountNameEncoded - selected account name from the url;
     * @return redirect to budgets page;
     */
    public function store($accountNameEncoded) {
        // decode the url account name;
        $accountName = urldecode($accountNameEncoded);
        
        // get all input names and values;
        $inputs = Request::all();
        
        // loop through the inputs;
        foreach ($inputs as $inputName => $inputValue) {
            // split the name string at "_"; index 0 should be "category2";
            // index 1 should be the category2 id;
            $inputNamePieces = explode("_", $inputName);
            
            // check if the input name in the format of "category2_{id}"
            // and that we have a numeric input value for the budget amount;
            if ($inputNamePieces[0] == "category2" && 
              is_numeric($inputNamePieces[1]) &&
              !empty($inputValue) &&
              is_numeric($inputValue)) {
                
                // set up new budget with values for saving
                $budgetValues = [
                  "transaction_category2_id" => $inputNamePieces[1],
                  "amount" => $inputValue,
                  "frequency_id" => 1];
                
                // see if the budget already exists and persist it with new
                // budget values;
                $budget = \Auth::user()
                  ->accounts()
                  ->where("name", "=", $accountName)
                  ->firstOrFail()
                  ->budgets()
                  ->where("transaction_category2_id", "=", $inputNamePieces[1])
                  ->firstOrNew($budgetValues);
                $budget->save();
            }
        }

        // Send a flash message of success to the view.
        session()->flash('flash_message', 'Your budgets have been saved!');
        
        // Redirect to transactions page.
        return redirect($accountNameEncoded . "/budgets");
    }
}
