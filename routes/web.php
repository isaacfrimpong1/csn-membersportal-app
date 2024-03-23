<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Route;

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
/**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/stock', 'App\Http\Controllers\StockController@show')->name('stock');

    Route::middleware(['check.admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'show'])->name('admin');
    });

    Route::middleware(['check.nonadmin'])->group(function () {
        // Routes for non-admin users go here
    });

*/

Route::get('/dashboard', 'DashboardController@getItems')->name('dashboard');

Route::get('/stock', 'App\Http\Controllers\StockController@show')->name('stock');

Route::get('/admin', 'App\Http\Controllers\AdminController@show')->name('admin');

Route::get('/search', [AdminController::class, 'searchBusiness']);

Route::get('/dashboard', [DashboardController::class, 'show'], function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
