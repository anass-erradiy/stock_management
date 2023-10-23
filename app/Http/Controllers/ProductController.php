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
            'ordred_quantity' => 0
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

    public function update(Product $product,){

    }

    public function destroy(Product $product){
        if(!in_array($product->id,Auth::user()->products->pluck('id')->toArray()) && !Auth::user()->hasRole('admin'))
            return response()->json([
                'error' => 'You do not have permession to do this action !'
            ]) ;
        $product->delete() ;
    }
}
