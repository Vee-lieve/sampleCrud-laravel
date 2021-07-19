<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user/create', [UsersController::class, 'createUser']); //create user data 
Route::get('/user/view-users', [UsersController::class, 'getUser']);  //get user data
// Route::get('/user/view-user/{id}', [UsersController::class, 'getUserByID']);   //query builder
Route::get('/user/view-user/{user}', [UsersController::class, 'getUserByID']);    //eloquent
Route::delete('/user/delete-user/{id}', [UsersController::class, 'deleteUserById']);