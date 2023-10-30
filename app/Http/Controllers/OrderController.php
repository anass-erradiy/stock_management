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
    public function __construct()
    {
        $this->middleware('role:admin|seller')->only(['index','sellerStatus']) ;
        $this->middleware('role:admin|buyer')->only(['store','update','destroy']) ;
        $this->middleware('role:admin')->only(['getTrashedOrder','getTrashedOrders','deleteTrashedOrders','deleteTrashedOrder']) ;

    }
    // get orders for the seller (orders that has seller products)
    public function index(){
        $orders = $this->productsInOrder() ;
        return response()->json(['orders' => $orders ]) ;
    }
    public function store(Request $request){
        $order_owner = User::findOrFail($request->get('user'));
        foreach($request->products as $product){
            if(!$this->checkProductAvailability($product))
                return response()->json([
                    'error' => 'this order can not be done ,the provided product is not available !'
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
    // get ordeers for the buyer
    public function buyerOrders(){
        $orders = Order::where('user_id',Auth::user()->id)->with('products')->get() ;
        return response()->json([
            'orders' => $orders
        ]) ;
    }
    // get an order with it's id for seller
    public function show(Order $order){
        if(count($this->productsInOrder())<1)
            return response()->json([
                'error' => 'you do not have access to this order !'
            ]);
        return response()->json([
            'order' => $order
        ]) ;
    }
    // get an order for buyer
    public function showBuyerOrder($id){
        if(!Auth::user()->hasRole('admin') && !$this->checkIfUserHasOrder($id)){
            return response()->json([
                'error' => 'as a buyer you do not have access to other sellers orders !'
            ]) ;
        }
        return response()->json([
            'order' => Order::find($id)
        ]);
    }
    // update order buyer side
    public function update(Order $order,Request $request){
        foreach($request->products as $product){
            if(!$this->checkProductAvailability($product))
                return response()->json([
                    'error' => 'this order can not be done ,the provided product is not available !'
                ],403) ;
        }
        if($order->order_status != 'sent')
            return response()->json([
                'error' => 'You can not modify this order !'
            ]);
        switch ($request->action) {
            case "add":
                if($this->checkIfProductalreadyOrdred($request->products,$order))
                    return response()->json([
                        'message' => 'Product aleady ordred !'
                    ]) ;
                if(!$this->attachProductsToOrder($request,$order))
                return response()->json([
                    'error' => 'somethng went wrong ,Try again !'
                ]) ;
                return response()->json([
                    'message' => 'order updated with success ,some proudct(s) added to the order'
                ]) ;
            case "delete":
                if(!$this->dettachProductsFromOrder($request,$order))
                    return response()->json([
                        'error' => 'some of your products can not be deleted from the order !'
                    ]) ;
                return response()->json([
                    'message' => 'product(s) deleted from the order with success !'
                ]) ;
            case "replace":
                $order->products()->detach() ;
                if(!$this->attachProductsToOrder($request,$order))
                    return response()->json([
                        'error' => 'somethng went wrong ,Try again !'
                ]) ;
                return response()->json([
                    'message' => 'order updated with success ,products replaced '
                ]) ;
            }
    }
    public function destroy(Order $order){
        if(!$order->order_status == 'sent' || !Auth::user()->hasRole('admin') && !$this->checkIfUserHasOrder($order->id) )
            // return $this->checkIfUserHasOrder($order->id) ;
            return response()->json([
                'error' => 'You can not delete this order !'
            ]) ;
        $order->delete() ;
        return response()->json([
            'message' => 'order deleted with success !'
        ]) ;
    }
    public function getTrashedOrder($id){
        $trashedOrder =  Order::withTrashed()->find($id) ;
        if(!$trashedOrder){
            return response()->json([
                'error' => 'no trashed order with the provided id !'
            ]) ;
        }
        return response()->json([
            'trashedOrder' => $trashedOrder
        ]) ;
    }
    public function getTrashedOrders(){

        $trashedOrders =  Order::withTrashed()->get() ;
        return response()->json([
            'trashedOrders' => $trashedOrders
        ]) ;
    }
    public function deleteTrashedOrders(){

        $status = Order::withTrashed()->forceDelete() ;
        if(!$status)
            return response()->json([
                'error' => 'something went wrong try again !'
            ]);
        return response()->json([
            'trashedOrders' => 'trashed deleted with success !'
        ]) ;
    }
    public function deleteTrashedOrder($id){
        $status = Order::withTrashed()->find($id)->forceDelete() ;
        if(!$status)
            return response()->json([
                'error' => 'something went wrong try again !'
            ]);
        return response()->json([
            'trashedOrders' => 'trashed order deleted with success !'
        ]) ;
    }
    // update the order status -> seller side
    public function sellerStatus($id,Request $request){
        if(count($this->productsInOrder())<1)
            return response()->json([
                'error' => 'you do not have access to this order'
            ]) ;
        Order::where('id' ,$id)->update([
            'order_status' => $request->order_status
        ]) ;
        return response()->json([
            'message' => 'status updated with success !'
        ]) ;
    }

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
    private function checkIfUserHasOrder($orderId){
        if(!in_array($orderId,Auth::user()->orders->pluck('id')->toArray()))
            return false ;
        return true ;
    }
    private function checkIfProductalreadyOrdred($products,$order){
        foreach($products as $product){
            if($order->products()->find($product['product_id'])) {
                return true ;
            }
        }
        return false ;
    }
    private function productsInOrder(){
        $seller_products = Product::where('user_id',Auth::user()->id)->pluck('id') ;
        $orders = Order::whereHas('products',function ($q) use ($seller_products){
            $q->whereIn('products.id',$seller_products) ;
        })->get() ;
        return $orders ;
    }
    private function attachProductsToOrder($request,$order){
        $order->products()->attach($request->products) ;
        if(!$this->updateProductQuantity($request->products))
            return false ;
        return true ;
    }
    private function dettachProductsFromOrder($request,$order){
        if(!$this->checkIfProductalreadyOrdred($request->products,$order))
            return false ;
        foreach($request->products as $product){
            $order->products()->detach($product['product_id']) ;
        }
        return true ;
    }
}
