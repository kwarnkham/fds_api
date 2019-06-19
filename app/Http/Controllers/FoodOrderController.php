<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\FoodOrder;

class FoodOrderController extends Controller
{
    public function store(Request $request) //proper error flow handling should be applied
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|digits_between:7,9',
            'address' => 'required',
            'cartItem' => 'required|min:1'
        ]);

        //new user, make one
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

        //calculating amount
        $amount = 0;
        foreach ($request->cartItem as $product) {
            $amount += $product['amount'];
        }

        //storing order
        $new_order = $new_user->food_orders()->create([
            'mobile' => $request->mobile,
            'address' => $request->address,
            'note' => $request->note,
            'amount' => $amount
        ]);

        //pivot table storing
        foreach ($request->cartItem as $product) {
            $new_order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        //resend stored order_id
        return ['message' => 'OK', 'order_id' => $new_order->id];
    }

    public function show(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric',
            'mobile' => 'required|numeric'
        ]);

        $order = FoodOrder::find($request->order_id);
        if ($order == null) {
            return ['message' => 'No Order'];
        }
        if ($order != null) {
            if ($order->user->mobile != $request->mobile) {
                return ['message' => 'No Order'];
            }
            if ($order->user->mobile == $request->mobile) {
                return ['message' => 'OK', 'order' => $order->load('user', 'products')];
            }
        }
    }
}
