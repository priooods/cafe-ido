<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TTransactionCheckoutTab extends Model
{
    protected $fillable = [
        'session_checkout',
        'm_status_tabs_id',
        'customer_name',
        'customer_phone',
        'notes',
        'cashier',
        'table_number',
        'path',
        'amount_paid',
        'amount_change',
    ];
}
