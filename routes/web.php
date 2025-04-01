<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculateDistributionController;
use Illuminate\Http\Request;

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
    return view('home');
})->name('home');

Route::get('/calculator', function (Request $request) {
    $initialData = $request->has('edit')
        ? session('calculator_data')
        : null;

    return view('calculator', [
        'initialData' => $initialData
    ]);
})->name('calculator');

Route::post('/calculate-distribution', [CalculateDistributionController::class, 'calculate'])->name('calculate.distribution');
Route::get('/inheritance-results', function () {
    return view('inheritance-results', [
        'shares' => session('results', []),
        'assets' => session('assets', []),
        'totalEstate' => session('totalEstate', 0),
        'deceasedInfo' => session('calculator_data.deceasedInfo', []),
    ]);
})->name('inheritance.results');
