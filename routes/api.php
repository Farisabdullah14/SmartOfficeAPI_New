<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::post('/TransaksiLampu', [\App\Http\Controllers\TransaksiLampuController::class, 'createTransaksiLampu']);
Route::get('/getMaxIdTransaksiOnStatus', [\App\Http\Controllers\TransaksiLampuController::class, 'getPemanggilanOn']);
Route::get('/getOnStatus', [\App\Http\Controllers\TransaksiLampuController::class, 'getMaxIdTransaksiOnStatus']);

Route::get('/cobaAmbilget/{id_lampu}', [\App\Http\Controllers\TransaksiLampuController::class, 'cobaAmbilget']);
Route::get('/getLatestTransaction/{id_lampu}', [\App\Http\Controllers\TransaksiLampuController::class, 'getLatestTransactionByLampId']);

Route::get('/ALLDataRuangan', [\App\Http\Controllers\RuanganController::class, 'ALLDataRuangan']);
Route::get('/isiRuangan/{idRuangan}', [\App\Http\Controllers\RuanganController::class, 'getDataByIdRuangan']);
Route::put('/updateTransaksiLampu', [\App\Http\Controllers\TransaksiLampuController::class, 'updateTransaksiLampu']);


// http://192.168.100.229:8181/api/getLatestTransaction/{id_lampu}



// Route::put('/updateTransaksiLampu/{id_lampu}', [\App\Http\Controllers\TransaksiLampuController::class, 'updateTransaksiLampu']);

Route::get('/LampuShowAllData', [\App\Http\Controllers\LampuController::class, 'showAllData']);

Route::get('/LampushowSelectedData', [\App\Http\Controllers\LampuController::class, 'showSelectedData']);
Route::get('/LampushowSelectedData', [\App\Http\Controllers\LampuController::class, 'showSelectedData']);

Route::get('/TransaksiLampuShowAllData', [\App\Http\Controllers\TransaksiLampuController::class, 'showAllData']);







//transaksi AC


Route::get('/ACgetPemanggilanOnData', [\App\Http\Controllers\TransaksiACController::class, 'getPemanggilanOn']);
Route::get('/ACshowSelectedData', [\App\Http\Controllers\TransaksiACController::class, 'showAllData']);
Route::get('/ACgetMaxIdTransaksiOnStatus', [\App\Http\Controllers\TransaksiACController::class, 'getMaxIdTransaksiOnStatus']);
Route::post('/createTransaksiAC', [\App\Http\Controllers\TransaksiACController::class, 'createTransaksiAC']);
// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);
// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);

// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);
Route::match(['put', 'post'], '/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);

// Route::post('/createTransaksiLampu', [\App\Http\Controllers\LampuController::class, 'createTransaksiLampu']);
