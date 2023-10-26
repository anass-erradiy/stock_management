<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProudctRequest;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin')->only(['trashedProducts','trashedProduct','deleteTrashedProduct','deleteTrashedProducts']) ;
        $this->middleware('role:admin|seller')->only('store') ;
    }
    public function index(){
        $products = Product::all() ;
        return response()->json([
            'products' => $products
        ]) ;
    }
    public function store(CreateProudctRequest $request){
        $product = Auth::user()->products()->create(
            $request->all()
            ) ;
            Product::find($product->id)->stock()->create([
            'total_quantity' => $request->quantity,
            'available_quantity' => $request->quantity ,
        ]) ;
        return response()->json([
            'message' => 'product created with success' ,
            'created_product' => $product
        ]) ;
    }

    public function show(Product $product){
        return response()->json([
            'product' => $product
            ]) ;
    }
    public function update(CreateProudctRequest $request,Product $product){
        // check if the seller has the updated product
        if(!in_array($product->id,Auth::user()->products->pluck('id')->toArray()) && !Auth::user()->hasRole('admin'))
            return response()->json([
                'error' => 'You do not have permession to do this action !'
            ]) ;
        $product->update($request->all()) ;
        if($request->has('quantity'))
            $product->stock->update([
                'available_quantity' => $request->quantity
            ]) ;
        return response()->json([
            'message' => 'Product updated successfully !'
        ]) ;
    }
    public function destroy(Product $product){
        // check if the seller has the product to delete
        if(!in_array($product->id,Auth::user()->products->pluck('id')->toArray()) && !Auth::user()->hasRole('admin'))
            return response()->json([
                'error' => 'You do not have permession to do this action !'
            ]) ;
        $product->delete() ;
        return response()->json([
            'message' => 'product delete with success !'
        ]) ;
    }

    public function trashedProducts(){
        $trashed_products = Product::withTrashed()->get() ;
        return response()->json([
            'trashedProducts' => $trashed_products
        ]) ;
    }
    public function trashedProduct($id){
        $trashed_product = Product::withTrashed()->find($id) ;
        return response()->json([
            'trashedProduct' => $trashed_product
        ]) ;
    }
    // single one
    public function deleteTrashedProduct($id){
        Product::find($id)->forceDelete() ;
        return response()->json([
            'message' => 'Product deleted permanently !'
        ]) ;
    }
    // all trashed
    public function deleteTrashedProducts(){
        Product::withTrashed()->forceDelete() ;
        return response()->json([
            'message' => 'Products deleted permanently !'
        ]) ;
    }
}
