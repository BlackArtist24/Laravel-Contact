<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::controller(UserController::class)->group(function () {
    Route::post('/getUser', 'getUser');
    Route::post('/getUsersList', 'getUsersList');
    Route::post('/updateData', 'updateData');
    Route::post('/deleteData', 'deleteData');
    Route::post('/createUser', 'createUser');
    Route::post('/signUp', 'signUp');
    Route::post('/login', 'login');
    Route::post('/queryCheck', 'queryCheck');  //// {modifier}
    // Route::get('/queryCheck/{modifier?}', 'queryCheck');  //// {modifier}
});
