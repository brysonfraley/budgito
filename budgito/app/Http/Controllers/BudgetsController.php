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
        
        // get all category1s and category2s;
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

        // get all budgets for the account;
        $budgets = \Auth::user()
          ->accounts()
          ->where("name", "=", $accountName)
          ->firstOrFail()
          ->budgets()
          ->select("transaction_category2_id", "amount")
          ->get()
          ->toArray();
        
        // create a look-up associative array of category2=>budget
        $budgetLookUp = [];
        foreach ($budgets as $budget) {
            $budgetLookUp[$budget["transaction_category2_id"]] = ["amount" => $budget["amount"]];
        }
        
        // set up a new array of all categories and budgets
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

//        // get all budgets for the account and corresponding categories;
//        $categoryBudgetsRaw = DB::table("transaction_category2s")
//          ->join("transaction_category1s", "transaction_category2s.transaction_category1_id", "=", "transaction_category1s.id")
//          ->join("transaction_types", "transaction_category1s.transaction_type_id", "=", "transaction_types.id")
//          ->leftJoin("budgets", "transaction_category2s.id", "=", "budgets.transaction_category2_id")
//          ->select("transaction_category2s.id as category2_id", 
//            "transaction_category2s.name as category2_name", 
//            "transaction_category1s.id as category1_id", 
//            "transaction_category1s.name as category1_name",
//            "transaction_types.name as transaction_type_name",
//            "budgets.amount as budget_amount")
//          ->where("budgets.account_id", "=", $accountId)
//          ->orWhereNull("budgets.account_id")
//          ->get();
//          dd($categoryBudgetsRaw);
//          
//        // organize the categories for the page view
//        // ex: [transType => [cat1 => [cat2_id=>[name=>cat2_name, budget_amount=>, cat2_id], cat1 => [cat2, cat2, cat2]]]
//        $categoryBudgetsOrganized = [];
//        foreach($categoryBudgetsRaw as $categoryBudget)
//            $category2Name = $category["category2_name"];
//            $category1Name = $category["category1_name"];
//            $transactionTypeName = $category["transaction_type_name"];
//            
//        // see if the category type is already recorded;
//        if (isset($allCategoriesOrganized[$categoryType])) {
//            // Yes, type recorded; see if the category1 is already recorded;
//            if (isset($allCategoriesOrganized[$categoryType][$category1Name])) {
//                // Yes, category1 recorded; record category2;
//                array_push($allCategoriesOrganized[$categoryType][$category1Name], $category2Name);
//            }
//            // else we have a new category1; record cat1 and cat2;
//            else {
//                $allCategoriesOrganized[$categoryType][$category1Name] = [$category2Name];
//            }
//        }
//        // else we have a new category type; record type, cat1, and cat2;
//        else {
//            $allCategoriesOrganized[$categoryTypeName] = [$category1Name => [$category2Name]];
//        }
//          
//          
//          foreach($allCategories as $category2Data) {
//              $category2Name = $category2Data["name"];
//              $category1Name = $category2Data["transaction_category1"]["name"];
//              $categoryType = $category2Data["transaction_category1"]["transaction_type"]["name"];
//              
//              // see if the category type is already recorded;
//              if (isset($allCategoriesOrganized[$categoryType])) {
//                  // Yes, type recorded; see if the category1 is already recorded;
//                  if (isset($allCategoriesOrganized[$categoryType][$category1Name])) {
//                      // Yes, category1 recorded; record category2;
//                      array_push($allCategoriesOrganized[$categoryType][$category1Name], $category2Name);
//                  }
//                  // else we have a new category1; record cat1 and cat2;
//                  else {
//                      $allCategoriesOrganized[$categoryType][$category1Name] = [$category2Name];
//                  }
//              }
//              // else we have a new category type; record type, cat1, and cat2;
//              else {
//                  $allCategoriesOrganized[$categoryType] = [$category1Name => [$category2Name]];
//              }
//          }
//          dd($allCategoriesOrganized);
        
        $data = [
            "accountName" => $accountName,
            "accountNameEncoded" =>$accountNameEncoded,
            "accounts" => $accounts,
            "pageTitle" => "Budgets",
            "showHeader" => true,
            "categoryBudgets" => $categoryBudgets
        ];
        return view('budgets', $data);
    }
    
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
