<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function() {
    Route::prefix('orders')->group(function() {
        Route::get('', 'OrderController@index');
        Route::get('{order}', 'OrderController@show');
        Route::get('{order}/status', 'OrderController@showStatus');
        Route::post('add', 'OrderController@addProduct');
        Route::post('coupon', 'OrderController@addCoupon');
        Route::post('submit', 'OrderController@submit');
        Route::post('{order}/submit/proof', 'OrderController@submitProof');
        Route::post('{order}/ship', 'ShipmentController@create');
        Route::post('{order}/cancel', 'OrderController@cancel');
    });

    Route::get('shipments/{shipment}/status', 'ShipmentController@showStatus');

    Route::post('logout', 'Auth\LoginController@logout');
});

Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');