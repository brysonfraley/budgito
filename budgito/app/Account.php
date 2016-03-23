<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_type_id', 
        'name', 
        'balance'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
    
    // Accounts table relationship with users table;
    public function user() {
        return $this->belongsTo('App\User');
    }
    
    // Accounts table relationship with transactions table;
    public function transactions() {
        return $this->hasMany('App\Transaction');
    }
    
    // Accounts table relationship with account_types table;
    public function accountType() {
        return $this->belongsTo('App\AccountType');
    }
    
    // Accounts table relationship with budgets table;
    public function budgets() {
        return $this->hasMany('App\Budget');
    }
}
