<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TProductTab extends Model
{
    protected $fillable = [
        'name',
        'path',
        'desc',
        'price',
        'm_category_tabs_id',
        'm_status_tabs_id',
    ];

    public function category(){
        return $this->hasOne(MCategoryTab::class,'id', 'm_category_tabs_id');
    }

    public function status()
    {
        return $this->hasOne(MStatusTab::class, 'id', 'm_status_tabs_id');
    }
}
