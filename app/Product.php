<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function food_orders(){
        return $this->belongsToMany('App\FoodOrder');
    }
}
