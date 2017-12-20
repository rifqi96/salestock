<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;

class ShipmentController extends Controller
{
    public function showStatus(Shipment $shipment)
    {
        $shipment = Shipment::with('order')
            ->find($shipment->id);

        if(auth()->user()->id != $shipment->order->user_id) {
            return response()->json([
                'status' => 403,
                'code' => 0,
                'data' => "Forbidden request"
            ], 403);
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => [
                'status' => $shipment->status
            ]
        ], 200);
    }

    public function create(Order $order) {
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
                'data' => "Order status is invalid to be shipped"
            ], 400);
        }
        else if($order->shipment()){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => "This order has been shipped"
            ], 400);
        }
        else if(!$order->ship()){
            return response()->json([
                'status' => 500,
                'code' => 0,
                'data' => "Internal Server Error. Failed to make shipment."
            ], 500);
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
}
