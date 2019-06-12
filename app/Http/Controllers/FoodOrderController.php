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

        $amount=0;
        foreach($request->cartItem as $product){
            $amount += $product['amount'];
        }

        $new_order= $new_user->food_orders()->create([
            'mobile' => $request->mobile,
            'address' => $request->address,
            'note' => $request->note,
            'amount' => $amount
        ]);
        
        foreach($request->cartItem as $product){
            $new_order->products()->attach($product['id'], ['quantity'=>$product['quantity']]);
        }
        // $new_order->products()->attach();

        //record order [user_id, product_id, quantity, amount, address, mobile]
        //resend order id

        return ['order_id'=>$new_order->id];
    }
}
