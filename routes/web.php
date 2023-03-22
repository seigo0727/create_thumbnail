<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/media', [App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
Route::post('/media', [App\Http\Controllers\MediaController::class, 'upload'])->name('media.upload');
Route::get('/media/show/{alias}', [App\Http\Controllers\MediaController::class, 'show'])->name('media.show');
Route::delete('/media/{id}', [App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete');

