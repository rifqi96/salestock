<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function orderDetails() {
        return $this->hasMany('App\Models\OrderDetail');
    }

    public function shipment() {
        return $this->hasOne('App\Models\Shipment');
    }

    public function coupon() {
        return $this->belongsTo('App\Models\Coupon');
    }
}
