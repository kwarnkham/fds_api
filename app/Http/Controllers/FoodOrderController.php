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
            'amount' => $amount
        ]);

        //pivot table storing
        foreach ($request->cartItem as $product) {
            $new_order->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'note' => $product['note']
            ]);
        }

        //resend stored order_id
        return ['message' => 'OK', 'order_id' => $new_order->id];
    }

    public function track(Request $request)
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

    public function index()
    {
        $orders = FoodOrder::all();
        return ['message' => 'OK', 'orders' => $orders];
    }

    public function show(Request $request)
    {
        $request->validate([
            'order_id' => 'required| numeric'
        ]);

        $order = FoodOrder::find($request->order_id)->load('user', 'products.product_pictures');
        return  ['message' => 'OK', 'order' => $order];
    }

    public function update(Request $request)
    {
        $request->validate([
            'order_id' => 'required| numeric',
            'action' => 'required'
        ]);

        if ($request->action == 'confirm') {
            FoodOrder::where('id', $request->order_id)->update(['status' => 'confirmed']);
            return ['message' => 'OK'];
        }
        if ($request->action == 'deliver') {
            FoodOrder::where('id', $request->order_id)->update(['status' => 'delivered']);
            return ['message' => 'OK'];
        }
        if ($request->action == 'cancel') {
            $order = FoodOrder::where('id', $request->order_id)->first();
            if ($order->admin_note == null) {
                $note = 'Canceled at status ' . $order->status;
                FoodOrder::where('id', $request->order_id)->update(['status' => 'canceled', 'admin_note' => $note]);
            }
            if ($order->admin_note != null) {
                $note = $order->admin_note . '. Canceled at status ' . $order->status;
                FoodOrder::where('id', $request->order_id)->update(['status' => 'canceled', 'admin_note' => $note]);
            }
            return ['message' => 'OK'];
        }
        if ($request->action == 'complete') {
            FoodOrder::where('id', $request->order_id)->update(['status' => 'completed']);
            return ['message' => 'OK'];
        } else {
            return ['message' => 'Invalid Action'];
        }
    }

    public function update_admin_note(Request $request)
    {
        $request->validate([
            'order_id' => 'required| numeric',
            'admin_note' => 'required'
        ]);

        FoodOrder::where('id', $request->order_id)->update(['admin_note' => $request->admin_note]);
        return ['message' => 'OK'];
    }
}
