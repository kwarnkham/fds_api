<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ProductController extends Controller
{
    public function store(Request $request){
        //1. validate
        //2. store product
        //3. store pics from the stored prodcut
        //4. return the product info
        
        // $files = $request->files;
        // return $request->files;
        $data= [];
        foreach($request->file('files') as $img){
            $path=Storage::putFile('public/product_pictures', new File($img)); //stored in storage
            array_push($data, $path);
        }
        return $data;
        // return $request->files;
    }
}
