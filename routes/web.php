<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MyTransactionController;
use App\Http\Controllers\ProductGalleryController;
use App\Http\Controllers\ProductCategoryController;
use App\Models\MailActivators;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::name('dashboard.')->prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        Route::middleware(['admin'])->group(function () {
            Route::resource('product', ProductController::class);
            Route::resource('category', ProductCategoryController::class);
            Route::resource('product.gallery', ProductGalleryController::class)->shallow()->only([
                'index', 'create', 'store', 'destroy'
            ]);
            Route::resource('transaction', TransactionController::class)->only([
                'index', 'show', 'edit', 'update'
            ]);
            Route::resource('user', UserController::class)->only([
                'index', 'edit', 'update', 'destroy'
            ]);
        });
    });
});

Route::get('mailactivate', function (Request $request) {

    $getToken = MailActivators::where(['name' => $request->name, 'token' => $request->token])->first();
    // dd($getToken);
    if ($getToken) {
        User::where('email', $request->name)->update(['email_verified_at' => Carbon::now()->toDateTimeString()]);
        return redirect('https://gemaindonesia.id');
    } else {
        return redirect('https://gemaindonesia.id');
    }
});
