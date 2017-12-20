<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipment;

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
        else if(!$order->addCoupon($request->coupon_id)){
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
        else if(!$order->submit($request)){
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

    public function submitProof(Request $request, Order $order) {
        if($order->user_id != auth()->user()->id){
            return response()->json([
                'status' => 403,
                'code' => 0,
                'data' => "Order does not exists"
            ], 403);
        }

        $file = $request->image;
        $extension = $file->getClientOriginalExtension();
        $filename = Carbon::now()->format('Ymdhis') . '_order_id_' . $order->id . '.' . $extension;
        Storage::disk('uploads')->put($filename,  file_get_contents($file->getRealPath()));

        $order->submitProof($filename);

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function index()
    {
        if(auth()->user()->role != "admin") {
            return response()->json([
                'status' => 403,
                'code' => 0,
                'data' => "Forbidden request"
            ], 403);
        }

        $orders = Order::with('orderDetails', 'shipment', 'coupon', 'user')->get();

        foreach($orders as $order){
            $order->user->setHidden(['api_token', 'password']);
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $orders
        ], 200);
    }

    public function show(Order $order)
    {
        if(auth()->user()->role != "admin") {
            return response()->json([
                'status' => 403,
                'code' => 0,
                'data' => "Forbidden request"
            ], 403);
        }

        $order = Order::with('orderDetails', 'shipment', 'coupon', 'user')
            ->find($order->id);

        $order->user->setHidden(['api_token', 'password']);

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ], 200);
    }

    public function cancel(Order $order)
    {
        if(auth()->user()->role != "admin") {
            return response()->json([
                'status' => 403,
                'code' => 0,
                'data' => "Forbidden request"
            ], 403);
        }
        else if($order->status != 'Finalized') {
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Order status is invalid to be cancelled"
            ], 400);
        }
        else if(!$order->cancel()){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "Any product doesn't exists"
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'message' => 'Order has been canceled',
            'data' => $order
        ], 200);
    }
}
