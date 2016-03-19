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
    
    public function transactions() {
        return $this->hasMany('App\Transaction');
    }
    
    public function transactionCategory1() {
        return $this->belongsTo('App\TransactionCategory1');
    }

    public function budgets() {
        return $this->hasMany('App\Budget');
    }
}
