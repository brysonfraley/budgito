<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'date', 
      'transaction_category2_id', 
      'amount', 
      'merchant', 
      'description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
    
    public function account() {
        return $this->belongsTo('App\Account');
    }
    
    public function transactionCategory2() {
        return $this->belongsTo('App\TransactionCategory2');
    }
}
