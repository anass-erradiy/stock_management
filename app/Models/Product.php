<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'user_id' ,
        'description',
        'price',
    ];
    public function user(){
        return $this->belongsTo(User::class) ;
    }
    public function stock(){
        return $this->hasOne(Stock::class) ;
    }

    public function order(){
        return $this->belongsTo(Order::class ,'orders_products')->withPivot(['qte']) ;
    }
}
