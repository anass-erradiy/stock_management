<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// users
Route::controller(UserController::class)
    ->group(function () {
        Route::post('/login', 'login')->name('users.login');
        Route::post('/users/register', 'register')->name('users.register')->middleware('auth:sanctum');
        Route::get('/me', 'me')->name('users.me')->middleware('auth:sanctum');
        Route::get('/logout','logout')->name('users.logout')->middleware('auth:sanctum') ;
    });
Route::apiResource("users",UserController::class)->middleware("auth:sanctum");

// products
Route::controller(ProductController::class)
->group(function () {
        Route::get('/products/trashed', 'trashedProducts')->middleware('auth:sanctum')->name('products.trashedProducts');
        Route::get('/products/trashed/{id}', 'trashedProduct')->middleware('auth:sanctum')->name('products.trashedProduct');
        Route::delete('/products/deleteTrashedProduct/{id}', 'deleteTrashedProduct')->middleware('auth:sanctum')->name('products.deleteTrashedProduct');
        Route::delete('/products/deleteTrashedProducts', 'deleteTrashedProducts')->middleware('auth:sanctum')->name('products.deleteTrashedProducts');
    });
Route::apiResource("products",ProductController::class)->middleware("auth:sanctum");

// oreders
Route::controller(OrderController::class)
->group(function () {
    Route::get('orders/trashed' ,'getTrashedOrders')->middleware('auth:sanctum')->name('orders.getTrashedOrders') ;
    Route::get('orders/trashed/{id}','getTrashedOrder')->middleware('auth:sanctum')->name('orders.getTrashedOrder') ;
    Route::delete('orders/trashed','deleteTrashedOrders')->middleware('auth:sanctum')->name('orders.deleteTrashedOrders') ;
    Route::delete('orders/trashed/{id}','deleteTrashedOrders')->middleware('auth:sanctum')->name('orders.deleteTrashedOrder') ;
    Route::put('orders/sellerStatus/{id}','sellerStatus')->middleware('auth:sanctum')->name('orders.sellerStatus') ;
    Route::get('orders/showBuyerOrder/{id}','showBuyerOrder')->middleware('auth:sanctum')->name('orders.showBuyerOrder') ;
}) ;
Route::apiResource('orders',OrderController::class)->middleware('auth:sanctum') ;

