<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payer_wallet_id',
        'payee_wallet_id',
        'value'

    ];

                           
    public function walletPayer()
    {
        return $this->beLongsTo(Wallet::class, 'payer_wallet_id');
    }

    public function walletPayee()
    {
        return $this->beLongsTo(Wallet::class, 'payee_wallet_id');
    }
}
