<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return response([
            'status' => 200,
            'code' => 1,
            'data' => $products
        ]);
    }

    public function show(Product $product)
    {
        return response([
            'status' => 200,
            'code' => 1,
            'data' => $product
        ]);
    }
}
