<?php

use App\Events\Hello;
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

Route::get('/broadcast', function () {
    broadcast(new Hello());
    return 'broadcasted';
});

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::get('profile', 'App\Http\Controllers\AuthController@profile');

    Route::get('users', 'App\Http\Controllers\UserController@index');
    Route::get('users/{contact_id}', 'App\Http\Controllers\UserController@show');

    Route::post('send-message', 'App\Http\Controllers\MessageController@send');
    Route::get('messages', 'App\Http\Controllers\MessageController@index');
    Route::get('messages/{contact_id}', 'App\Http\Controllers\MessageController@messages');
});
