<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');


Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify');

Route::get('document', 'DocumentsController@show')->middleware('jwt.verify');
Route::post('document', 'DocumentsController@create')->middleware('jwt.verify');
Route::put('document/{id}', 'DocumentsController@update')->middleware('jwt.verify');
Route::delete('document/{id}', 'DocumentsController@delete')->middleware('jwt.verify');

Route::post('upload', 'DocumentsController@upload')->middleware('jwt.verify');