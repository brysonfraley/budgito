<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $data = [
        "pageTitle" => "Welcome",
        "showHeader" => true
    ];
    return view('welcome', $data);
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/dashboard', 'DashboardController@index');
    Route::post('/dashboard', 'DashboardController@setAccount');
    
    Route::get('/accounts', 'AccountController@index');
    Route::post('/accounts', 'AccountController@store');
    Route::get('/accounts/add', 'AccountController@add');
    
    Route::post('/data/category1s', 'TransactionCategory1Controller@getData');
    Route::post('/data/category2s', 'TransactionCategory2Controller@getData');
    Route::post('/data/transactions', 'TransactionController@getData');
    
    
    
    Route::get('/{accountNameEncoded}/dashboard', 'DashboardController@index');
    
    Route::get('/{accountNameEncoded}/transactions', 
      'TransactionController@index');
    Route::post('/{accountNameEncoded}/transactions', 
      'TransactionController@store');
    Route::get('/{accountNameEncoded}/transactions/add', 
      'TransactionController@add');
    
    Route::get('/{accountNameEncoded}/budgets', 'BudgetsController@index');
    Route::post('/{accountNameEncoded}/budgets', 'BudgetsController@store');
    
    Route::get('/{accountNameEncoded}', function ($accountNameEncoded) {
        return redirect($accountNameEncoded . '/dashboard');
    });
});
