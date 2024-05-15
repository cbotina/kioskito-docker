<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     // return $request->user();
//     return true;
// });

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::get('users/{id}', [UserController::class, 'findUser']);

Route::group(['middleware'=>['jwt.verify']], function() {

    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/available', [ProductController::class, 'indexAvailable']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    // orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/products', [OrderController::class, 'addProduct']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::put('/orders/{id}/approve', [OrderController::class, 'approve']);
    Route::put('/orders/{id}/reject', [OrderController::class, 'reject']);
    Route::put('/orders/{id}/start', [OrderController::class, 'start']);
    Route::put('/orders/{id}/finish', [OrderController::class, 'finish']);
    Route::get('/orders/pending', [OrderController::class, 'indexPending']);
    Route::get('/orders/approved', [OrderController::class, 'indexApproved']);
    Route::get('/orders/finished', [OrderController::class, 'indexFinished']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/products', [OrderController::class, 'showProducts']);
    Route::get('/orders/{id}/user', [OrderController::class, 'showUser']);
    Route::get('/orders/{id}/details', [OrderController::class, 'showDetails']);
    Route::get('/user/{id}/orders', [OrderController::class, 'indexUserOrders']);
    Route::delete('/reset', [OrderController::class, 'resetApplication']);


});


// Route::controller(AuthController::class)->group(function () {
//     Route::post('/', 'register');
//     Route::post('/login', 'login');
// });
