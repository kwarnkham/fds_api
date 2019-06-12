<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\FoodOrder;

class FoodOrderController extends Controller
{
    public function store(Request $request)
    {
        //new user create one
        $user = User::where('mobile', $request->mobile)->get();
        if (count($user) == 0) {
            $new_user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'address' => $request->address
            ]);
        }
        //old user, update info
        if (count($user) > 0) {
            $new_user = User::find($user[0]->id);
            $new_user->name = $request->name;
            $new_user->address = $request->address;
            $new_user->save();
        }
        //store order info
        $new_order = FoodOrder::create([
            'user_id' => $new_user->id,
            'mobile' => $request->mobile,
            'address' => $request->address
        ]);
        //record order [user_id, product_id, quantity, amount, address, mobile]
        //resend order id

        return $new_order;
    }
}
