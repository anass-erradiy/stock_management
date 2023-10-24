<?php

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
        Route::post('/register', 'register')->name('users.register')->middleware('auth:sanctum');
        Route::get('/me', 'me')->name('users.me')->middleware('auth:sanctum');
        Route::get('/logout','logout')->name('users.logout')->middleware('auth:sanctum') ;
    });
Route::apiResource("users",UserController::class)->middleware("auth:sanctum");

// products
Route::controller(ProductController::class)
->group(function () {
        Route::get('/products/trashed', 'trashedProducts')->middleware('auth:sanctum')->name('products');
        Route::get('/products/trashed/{id}', 'trashedProducts')->middleware('auth:sanctum')->name('products');
        Route::delete('/products/deleteTrashedProduct/{id}', 'deleteTrashedProduct')->middleware('auth:sanctum')->name('products');
        Route::delete('/products/deleteTrashedProducts', 'deleteTrashedProducts')->middleware('auth:sanctum')->name('products');
    });
Route::apiResource("products",ProductController::class)->middleware("auth:sanctum");

