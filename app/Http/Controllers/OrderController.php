<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Helper functions
    private function checkProductAvailability($product){
        $productToFind = Product::find($product['product_id']) ;
        if(!$productToFind || $productToFind->stock->available_quantity < $product['qte'])
            return false ;
        return true ;
    }
    private function updateProductQuantity($products){
        foreach($products as $product){
            $stocked_product = Stock::where('product_id',$product['product_id'])->first() ;
            if(!$stocked_product)
                return false ;
            $stocked_product->update([
                    'available_quantity' => $stocked_product->available_quantity - $product['qte'] ,
                    'ordred_quantity' => $stocked_product->ordred_quantity + $product['qte']
                ]) ;
        }
        return true ;
    }

    public function index(){
        return response()->json([
            Order::all()
        ]) ;
    }
    // return Order::where('user_id',Auth::user()->id)->with('products')->get() ;
    public function store(Request $request){
        $order_owner = User::findOrFail($request->get('user'));
        foreach($request->products as $product){
            if(!$this->checkProductAvailability($product))
                return response()->json([
                    'error' => 'this order can not be done ,the product you provided is not available !'
                ],403) ;
        }
        $order = $order_owner->orders()->create() ;
        $order->products()->attach($request->products) ;
        if(!$this->updateProductQuantity($request->products))
            return response()->json([
                'error' => 'something went wrong !'
            ]) ;
        return response()->json([
            'message' => 'order submited !'
        ]) ;
    }
    public function show(){
        //
    }
    public function update(){
        //
    }
    public function destroy(){
        //
    }

    // helpers functions


}
