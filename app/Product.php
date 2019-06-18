<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function food_orders(){
        return $this->belongsToMany('App\FoodOrder')->withTimestamps()->withPivot('quantity');
    }

    public function product_pictures(){
        return $this->hasMany('App\ProductPicture');
    }

    protected $fillable = [
        'name', 'price','description'
    ];

    
}
