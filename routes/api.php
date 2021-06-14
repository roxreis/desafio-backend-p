<?php

use Illuminate\Support\Facades\Route;
use App\Models\Storekeeper;
use App\Models\Customer;
use App\Http\Controllers\TransactionController;

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

Route::get('/', function () {
    Customer::factory()->create();
    Storekeeper::factory()->create();
    return view('welcome');
});

 
Route::post('/transaction', [TransactionController::class, 'transaction']);