<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function createProduct(Request $request){
        try {
            $request->validate([
                'product_name' => "required" ,
                'quantity' => 'required|min:1|integer' ,
                'description' => 'string' ,
                'price' => 'required|numeric'
            ]) ;
            $product = Auth::user()->products()->create([
                "product_name" => $request->product_name,
                'description' => $request->description,
                'price' => $request->price
                ]) ;
                Product::find($product->id)->stock()->create([
                'total_quantity' => $request->quantity,
                'available_quantity' => $request->quantity
            ]) ;
            return response()->json([
                'message' => 'product created with success' ,
                $product
            ]) ;
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]) ;
        }
    }
}
