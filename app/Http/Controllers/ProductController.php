<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Product;
use App\ProductPicture;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        //1. validate
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'files' => 'required|array',
            'files.*' => 'image'
        ]);
        //2. store product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description
        ]);
        //3. store pics from the stored prodcut
        $data = [];
        foreach ($request->file('files') as $img) {
            $path = Storage::putFile('public/product_pictures', new File($img)); //stored in storage
            // $stored_image=public_path('storage/product_pictures/'.basename($path));
            $stored_image = 'storage/product_pictures/' . basename($path);
            array_push($data, $stored_image);
        }

        foreach ($data as $product_picture) {
            ProductPicture::create([
                'product_id' => $product->id,
                'name' => $product_picture
            ]);
        }
        //4. return the product info
        return ['message' => 'Successfully Added'];
    }

    public function index()
    {
        $products = Product::all();
        return ['products' => $products->load('product_pictures')];
    }
}
