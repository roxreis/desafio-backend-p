<?php

namespace App\Models;

 
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer; 
use App\Models\Transaction; 

class Wallet extends Model
{
    protected $fillable = [
        '$user_id',
        '$balance'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function customer()
    {
        return $this->beLongsTo(Customer::class);
    }

    public function deposit($value, $id)
    {   
        $deposit = Wallet::where('user_id',$id)->first();
        $deposit->balance = $this->attributes['balance'] + $value;
        $deposit->save();
        
    }

    public function withdraw($value, $id)
    {
        $deposit = Wallet::where('user_id',$id)->first();
        $deposit->balance = $this->attributes['balance'] - $value;
        $deposit->save();
    }

    public static function createCustomerWallet($data)
    {
        $wallet = new Wallet;
        $wallet->user_id = $data['payer_id'];
        $wallet->balance = 0;
        $wallet->save();
    }

    public static function createStorekeeperWallet($data)
    {
        $wallet = new Wallet;
        $wallet->user_id = $data['payee_id'];
        $wallet->balance = 0;
        $wallet->save();

    }

}
