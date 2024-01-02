<?php

use App\Http\Controllers\CarController;
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



Route::get('/save-car-makes', [CarController::class, 'saveCarMakesFromApi'])->name('save.car.makes');
Route::get('/save-car-models', [CarController::class, 'saveCarModelsFromApi'])->name('save.car.models');

