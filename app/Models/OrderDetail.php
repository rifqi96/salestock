<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id','product_id','qty'
    ];

    public function incrementQty($qty) {
        $this->qty += $qty;
        return $this->save();
    }

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function product() {
        return $this->hasOne('App\Models\Product');
    }
}
