<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','coupon_id','payment_proof','status'
    ];

    public function addProduct($data) {
        $product = Product::find($data->product_id);
        $od = OrderDetail::where([
            ['order_id', $this->id],
            ['product_id', $data->product_id]
        ])->first();

        $totalQty = $data->qty;
        if($od){
            $totalQty = $od->qty + $data->qty;
        }

        if(($product->qty - $totalQty) < 0) {
            return false;
        }

        if($od){
            return $od->incrementQty($data->qty);
        }

        $od = new OrderDetail([
            'order_id' => $this->id,
            'product_id' => $data->product_id,
            'qty' => $data->qty
        ]);

        return $od->save();
    }

    public function addCoupon($coupon_id) {
        $coupon = Coupon::find($coupon_id);
        $today = Carbon::today();

        if($today < $coupon->start_at || $today > $coupon->end_at) {
            return false;
        }

        $this->coupon_id = $coupon_id;

        return $this->save();
    }

    public function submit($customer) {
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->email = $customer->email;
        $this->status = "Finalized";

        // Check if coupon is still valid
        if($this->coupon_id){
            $coupon = Coupon::find($this->coupon_id);
            $today = Carbon::today();

            if($today < $coupon->start_at || $today > $coupon->end_at) {
                return false;
            }
        }

        // Check if products exist and all product stocks are sufficient
        foreach($this->orderDetails as $od) {
            $product = Product::find($od->product_id);
            if(!$product) {
                return false;
            }
            else if($od->qty > $product->qty) {
                return false;
            }
        }

        // Validation passes, decrement quantity
        foreach($this->orderDetails as $od) {
            $product = Product::find($od->product_id);
            $product->qty -= $od->qty;
            if(!$product->save()){
                return false;
            }
        }

        return $this->save();
    }

    public function submitProof($filename) {
        $this->payment_proof = $filename;

        return $this->save();
    }

    public function ship() {
        $shipment = new Shipment([
            'order_id' => $this->id,
            'status' => 'Shipping'
        ]);
        if(!$shipment->save()) {
            return false;
        }

        $this->status = "Processed";

        return $this->save();
    }

    public function cancel() {
        // Check if products exist
        foreach($this->orderDetails as $od) {
            $product = Product::find($od->product_id);
            if(!$product) {
                return false;
            }
        }

        // Validation passes, restoring quantity
        foreach($this->orderDetails as $od) {
            $product = Product::find($od->product_id);
            $product->qty += $od->qty;
            if(!$product->save()) {
                return false;
            }
        }

        $this->status = "Canceled";

        return $this->save();
    }
    
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
