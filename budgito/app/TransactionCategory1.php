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
    
    public function transactionCategory2s() {
        return $this->hasMany('App\TransactionCategory2');
    }
    
    public function transactionType() {
        return $this->belongsTo('App\TransactionType');
    }
}
