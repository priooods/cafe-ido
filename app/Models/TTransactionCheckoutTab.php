<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TTransactionCheckoutTab extends Model
{
    protected $fillable = [
        'order_id',
        'm_status_tabs_id',
        'customer_name',
        'customer_phone',
        'notes',
        'cashier',
        'bill',
        'table_number',
        'amount_paid',
        'amount_change',
    ];

    public function item()
    {
        return $this->hasMany(TTransactionTab::class, 't_transaction_checkout_tabs_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(MStatusTab::class, 'id', 'm_status_tabs_id');
    }
}
