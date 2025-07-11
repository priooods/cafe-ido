<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TTransactionTab extends Model
{
    protected $fillable = [
        't_transaction_checkout_tabs_id',
        't_product_tabs_id',
        'count'
    ];

    public function product()
    {
        return $this->hasOne(TProductTab::class, 'id', 't_product_tabs_id');
    }
}
