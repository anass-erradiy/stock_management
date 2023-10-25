<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable= [
        'user_id',
        'order_status'
    ] ;

    public function products() {
        return $this->belongsToMany(Product::class,'orders_products')->withPivot(['qte']) ;
    }

    public function user(){
        return $this->belongsTo(User::class) ;
    }
}
