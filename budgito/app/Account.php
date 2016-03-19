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
    
    public function user() {
        return $this->belongsTo('App\User');
    }
    
    public function transactions() {
        return $this->hasMany('App\Transaction');
    }
    
    public function accountType() {
        return $this->belongsTo('App\AccountType');
    }
    
    public function budgets() {
        return $this->hasMany('App\Budget');
    }
}
