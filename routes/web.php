<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apis\GetNewsController;
use App\Http\Controllers\Users\UsersController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('{any}', [UsersController::class, 'index'])->name('users.index')->where('any', '.*');
Route::get('/', [UsersController::class, 'index'])->name('users.index');
Route::get('/api/index', [GetNewsController::class, 'index'])->name('api.index');
Route::get('/api/insertArticles', [GetNewsController::class, 'insertArticles'])->name('api.insertArticles');
Route::get('/api/selectArticles', [GetNewsController::class, 'selectArticles'])->name('api.selectArticles');
Route::post('/api/changeIsRecommended', [GetNewsController::class, 'changeIsRecommended'])->name('api.changeIsRecommended');





Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
