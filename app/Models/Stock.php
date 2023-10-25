<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Stock extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'product_id',
        'ordred_quantity',
        'available_quantity'
    ] ;

    public function products(){
        return $this->hasMany(Product::class) ;
    }

}
