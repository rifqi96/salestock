<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function addProduct(Request $request) {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => $validator->messages()
            ], 400);
        }

        $order = Order::where([
                ['user_id', auth()->user()->id],
                ['status', 'Processing']
            ])
            ->first();

        if($order) {
            if($order->addProduct($request)) {
                $id = $order->id;

                $order = Order::with(['orderDetails', 'coupon'])
                    ->find($id);
            }
            else{
                return response()->json([
                    'status' => 400,
                    'code' => 0,
                    'data' => "Product stock is insufficient"
                ], 400);
            }
        }
        else {
            $order = new Order([
                'user_id' => auth()->user()->id,
                'status' => 'Processing'
            ]);
            if($order->save() && $order->addProduct($request)){
                $id = $order->id;

                $order = Order::with(['orderDetails', 'coupon'])
                    ->find($id);
            }
            else{
                return response()->json([
                    'status' => 400,
                    'code' => 0,
                    'data' => "Product stock is insufficient"
                ], 400);
            }
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function addCoupon(Request $request) {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'id' => 'required|integer|exists:orders,id',
            'coupon_id' => 'required|integer|exists:coupons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => $validator->messages()
            ], 400);
        }

        $order = Order::with('coupon', 'orderDetails')
            ->find($request->id);

        if(!$order || $order->user_id != auth()->user()->id || $order->status != "Processing") {
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Order does not exists"
            ], 400);
        }

        if(!$order->addCoupon($request->coupon_id)){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Coupon is expired"
            ], 400);
        }

        $order = Order::with('coupon', 'orderDetails')
            ->find($request->id);

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function submit(Request $request) {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string|min:6',
            'email' => 'required|email',
            'address' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => $validator->messages()
            ], 400);
        }

        $order = Order::where([
                ['user_id', auth()->user()->id],
                ['status', 'Processing']
            ])
            ->first();

        if(!$order){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Order does not exists"
            ], 400);
        }

        if(!$order->submit($request)){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Validation Error: Product stock is insufficient or coupon is expired"
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }
}
