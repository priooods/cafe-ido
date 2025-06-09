<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TTransactionTab extends Model
{
    protected $fillable = [
        'session_product',
        't_transaction_checkout_tabs_id',
        't_product_tabs_id',
        'notes'
    ];
}
