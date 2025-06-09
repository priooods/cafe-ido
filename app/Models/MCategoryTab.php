<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCategoryTab extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'm_status_tabs_id',
    ];

    public function status()
    {
        return $this->hasOne(MStatusTab::class, 'id', 'm_status_tabs_id');
    }

    public function product(){
        return $this->hasMany(TProductTab::class, 'm_category_tabs_id','id');
    }
}
