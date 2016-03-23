<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory1 extends Model
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
    
    // transaction_category1s table relationship with transaction_category2s
    //  table;
    public function transactionCategory2s() {
        return $this->hasMany('App\TransactionCategory2');
    }
    
    // transaction_category1s table relationship with transaction_types table;
    public function transactionType() {
        return $this->belongsTo('App\TransactionType');
    }
}
