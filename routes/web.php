<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\TeleMessageController;
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

// Route::get('/get-tuban-data', [TeleMessageController::class, 'getPlantTubanData']);

Route::post('send-message', [TeleMessageController::class, 'sendMessage'])->name('send_message');
