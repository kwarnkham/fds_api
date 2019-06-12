<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    protected $fillable = [
        'user_id','amount', 'address', 'mobile'
    ];
}
