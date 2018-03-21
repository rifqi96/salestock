<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        if(auth()->user()->role != "admin") {
            abort(403);
        }

        $orders = Order::with('orderDetails', 'shipment', 'coupon', 'user')->get();

        foreach($orders as $order){
            $order->user->setHidden(['api_token', 'password']);
        }

        return response([
            'status' => 200,
            'code' => 1,
            'data' => $orders
        ]);
    }

    public function show(Order $order)
    {
        if(auth()->user()->role != "admin") {
            abort(403);
        }

        return response([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function showStatus(Order $order)
    {
        if(auth()->user()->id != $order->user_id) {
            abort(403);
        }

        return response([
            'status' => 200,
            'code' => 1,
            'data' => [
                'status' => $order->status
            ]
        ]);
    }

    public function addProduct(Request $request) {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            abort(412, $validator->messages());
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
                abort(400, "Product stock is insufficient");
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
                abort(400, "Product stock is insufficient");
            }
        }

        return response([
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
            abort(412, $validator->messages());
        }

        $order = Order::with('coupon', 'orderDetails')
            ->find($request->id);

        if(!$order || $order->user_id != auth()->user()->id || $order->status != "Processing") {
            abort(400, "Order does not exists");
        }
        else if(!$order->addCoupon($request->coupon_id)){
            abort(400, "Coupon is expired");
        }

        $order = Order::with('coupon', 'orderDetails')
            ->find($request->id);

        return response([
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
            abort(412, $validator->messages());
        }

        $order = Order::where([
                ['user_id', auth()->user()->id],
                ['status', 'Processing']
            ])
            ->first();

        if(!$order){
            abort(400, "Order does not exists");
        }
        else if(!$order->submit($request)){
            abort(400, "Validation Error: Product stock is insufficient or coupon is expired");
        }

        return response([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function submitProof(Request $request, Order $order) {
        if($order->user_id != auth()->user()->id){
            abort(400, "Order does not exists");
        }

        $file = $request->image;
        $extension = $file->getClientOriginalExtension();
        $filename = Carbon::now()->format('Ymdhis') . '_order_id_' . $order->id . '.' . $extension;
        Storage::disk('uploads')->put($filename,  file_get_contents($file->getRealPath()));

        $order->submitProof($filename);

        return response([
            'status' => 200,
            'code' => 1,
            'data' => $order
        ]);
    }

    public function cancel(Order $order)
    {
        if(auth()->user()->role != "admin") {
            abort(403);
        }
        else if($order->status != 'Finalized') {
            abort(400,"Order status is invalid to be cancelled");
        }
        else if(!$order->cancel()){
            abort(400, "Any product doesn't exists");
        }

        return response([
            'status' => 200,
            'code' => 1,
            'message' => 'Order has been canceled',
            'data' => $order
        ]);
    }
}
