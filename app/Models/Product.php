<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'product_name',
        'user_id' ,
        'description',
        'price',
    ];
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class) ;
    }
    public function stock() : HasOne
    {
        return $this->hasOne(Stock::class) ;
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class ,'orders_products')->withPivot(['qte']) ;
    }
}
