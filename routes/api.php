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

Route::controller(UserController::class)
    ->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::get('/checkAuth', 'checkAuth')->name('checkAuth')->middleware('auth:sanctum');
        Route::get('/logout','logout')->name('logout')->middleware('auth:sanctum') ;
        Route::post('/users/edit/{id}','edit')->name('users.edit')->middleware('auth:sanctum') ;
        Route::delete('/users/delete/{id}','delete')->name('users.delete')->middleware('auth:sanctum','checkRole:admin') ;

});
Route::controller(ProductController::class)
    ->group(function () {
        Route::get('/products', 'productsList')->middleware('auth:sanctum')->name('productsList');
        Route::post('/products/create', 'createProduct')->middleware('auth:sanctum','checkRole:seller')->name('createProduct');
});
