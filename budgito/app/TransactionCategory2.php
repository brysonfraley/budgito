<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory2 extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
    
    // transaction_category2s table relationship with transactions table;
    public function transactions() {
        return $this->hasMany('App\Transaction');
    }
    
    // transaction_category2s table relationship with transaction_category1s
    //  table;
    public function transactionCategory1() {
        return $this->belongsTo('App\TransactionCategory1');
    }

    // transaction_category2s table relationship with budgets table;
    public function budgets() {
        return $this->hasMany('App\Budget');
    }
}
