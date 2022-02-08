<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\ProjectController;

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


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('userlist', [UserController::class, 'userlist']);

    Route::get('transactions', [TransactionController::class, 'all']);
    Route::post('transaction/upload', [TransactionController::class, 'uploadBukti']);
    Route::post('transaction/approve', [TransactionController::class, 'approve']);
    Route::get('alltrans', [TransactionController::class, 'show']);
    Route::post('checkout', [TransactionController::class, 'checkout']);
});

Route::post('createproject', [ProjectController::class, 'store']);
Route::get('projects', [ProjectController::class, 'index']);
Route::get('products', [ProductController::class, 'all']);
Route::get('categories', [ProductCategoryController::class, 'all']);

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
