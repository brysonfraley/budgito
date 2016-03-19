<?php

namespace App\Http\Controllers;

use App\Http\Requests;
//use Illuminate\Http\Request;
use Request;
use \App\Input;

use App\Account;
use App\AccountType;

class AccountController extends Controller
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
     * Show the application accounts page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get the users accounts
        $accounts =  \Auth::user()
          ->accounts()
          ->with('accountType')
          ->orderBy('updated_at', 'desc')
          ->get()
          ->toArray();
        
        // collect data to pass to the page view
        $data = [
            "pageTitle" => "Accounts",
            "showHeader" => true,
            "accounts" => $accounts
        ];
        
        // load the page view
        return view('accounts', $data);
    }
    
    /**
     * Show the application add account page.
     * 
     * @return type
     */
    public function add()
    {
        $data = [
            "pageTitle" => "Add Account",
            "showHeader" => true
        ];
        return view('accounts_add', $data);
    }
    
    /**
     * Store a new account for the user.
     * 
     * @return type
     */
    public function store()
    {
        // Accounts table needs account_type_id field...
        // Get the account type name from form and find it's id.
        $account_type = Request::get("account_type");
        $account_type_id = AccountType::where("name", $account_type)
          ->firstOrFail()
          ->id;

        // Set up a new account model obj and set the fillable fields.
        $account = new Account;
        $account->account_type_id = $account_type_id;
        $account->name = Request::get("name");
        $account->balance = Request::get("balance");
        
        // Save the user's account model obj
        \Auth::user()->accounts()->save($account);
        
        // Send a flash message of success to the view.
        session()->flash('flash_message', 'Your new account has been added!');
        
        // Redirect to accounts page.
        return redirect("accounts");
    }
}
