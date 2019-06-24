<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/user/create', 'UserController@store');
// Route::post('/api_token/create', 'ApiTokenController@store');

// Route::middleware('auth:api')->post('/api_token/destroy', 'ApiTokenController@destroy');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('/order/submit', 'FoodOrderController@store');
Route::get('/order/track', 'FoodOrderController@track');
Route::get('/order/index', 'FoodOrderController@index');
Route::get('/order/show', 'FoodOrderController@show');
Route::post('/order/update', 'FoodOrderController@update');

Route::post('/product/create', 'ProductController@store');
Route::get('/product/index', 'ProductController@index');
