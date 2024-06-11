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
Route::put('/updateTransaksiLampu', [\App\Http\Controllers\TransaksiLampuController::class, 'updateTransaksiLampu']);


Route::get('/isiRuangan/{idRuangan}', [\App\Http\Controllers\RuanganController::class, 'getDataByIdRuangan']);
Route::get('/getjumlahDV/{idRuangan}', [\App\Http\Controllers\RuanganController::class, 'getjumlahDV']);


Route::get('/ambilDataDanGabungkan/{idRuangan}', [\App\Http\Controllers\RuanganController::class, 'ambilDataDanGabungkan']);


// Route::get('/getNumberOfDevicesByRuanganId/{idRuangan}', [\App\Http\Controllers\RuanganController::class, 'getNumberOfDevicesByRuanganId']);
// http://192.168.100.229:8181/api/getLatestTransaction/{id_lampu}



// Route::put('/updateTransaksiLampu/{id_lampu}', [\App\Http\Controllers\TransaksiLampuController::class, 'updateTransaksiLampu']);

Route::get('/LampuShowAllData', [\App\Http\Controllers\LampuController::class, 'showAllData']);
Route::get('/LampushowSelectedData', [\App\Http\Controllers\LampuController::class, 'showSelectedData']);
Route::get('/LampushowSelectedData', [\App\Http\Controllers\LampuController::class, 'showSelectedData']);
Route::get('/TransaksiLampuShowAllData', [\App\Http\Controllers\TransaksiLampuController::class, 'showAllData']);







//transaksi AC


Route::get('/ACgetPemanggilanOnData', [\App\Http\Controllers\TransaksiACController::class, 'getPemanggilanOn']);
Route::get('/ACshowSelectedData', [\App\Http\Controllers\ACController::class, 'showAllData']);
Route::get('/ACgetMaxIdTransaksiOnStatus', [\App\Http\Controllers\TransaksiACController::class, 'getMaxIdTransaksiOnStatus']);
Route::get('/cobaAmbilgetAC/{AC_id}', [\App\Http\Controllers\TransaksiACController::class, 'cobaAmbilgetAC']);
Route::post('/createTransaksiAC', [\App\Http\Controllers\TransaksiACController::class, 'createTransaksiAC']);
// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);
// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);

// Route::put('/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);
Route::match(['put', 'post'], '/updateTransaksiAC_Kecepatan_kipas', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiAC_Kecepatan_kipas']);

Route::get('/searchDataByIdAC/{idAC}', [\App\Http\Controllers\ACController::class, 'searchDataByIdAC']);

Route::get('/searchByIdAC', [\App\Http\Controllers\ACController::class, 'showAllData']);




Route::get('/getDataTerbaruByIdAC/{AC_id}', [\App\Http\Controllers\HistoryTransaksiAcController::class, 'getDataTerbaruByIdAC']);
Route::post('/getpergerakanac/{AC_id}', [\App\Http\Controllers\TransaksiACController::class, 'getpergerakanac']);


Route::get('/getLatestTransactionAC/{AC_id}', [\App\Http\Controllers\TransaksiACController::class, 'getLatestTransactionAC']);
Route::post('/updateTransaksiOff/{AC_id}', [\App\Http\Controllers\TransaksiACController::class, 'updateTransaksiOff']);



Route::get('/LihatDataAktivitasPengguna/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'LihatDataAktivitasPengguna']);
Route::get('/LihatDataAktivitasSemuaPengguna', [\App\Http\Controllers\AktivitasPengguna::class, 'LihatDataAktivitasSemuaPengguna']);
Route::get('/LihatTransaksiLampu/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'LihatTransaksiLampu']);
Route::get('/PenggunaanDayaLampuPerHari/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'PenggunaanDayaLampuPerHari']);
Route::get('/JumlahPerangkatAktivitasPengguna/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'JumlahPerangkatAktivitasPengguna']);
Route::get('/melihatDayaLampuPerHari', [\App\Http\Controllers\AktivitasPengguna::class, 'melihatDayaLampuPerHari']);
Route::get('/melihatDayaLampuPerHariPengguna/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'melihatDayaLampuPerHariPengguna']);
Route::get('/hitungKwhPerHari/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'hitungKwhPerHari']);
Route::get('/hitungKwhPerMinggu/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'hitungKwhPerMinggu']);
Route::get('/LihatTransaksiLampuMingguan/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'LihatTransaksiLampuMingguan']);
Route::get('/aaa', [\App\Http\Controllers\AktivitasPengguna::class, 'aaa']);
Route::get('/LihatTransaksiLampuDanHitungKwhPerHari/{id_pengguna}', [\App\Http\Controllers\AktivitasPengguna::class, 'LihatTransaksiLampuDanHitungKwhPerHari']);
Route::get('/hitungKwh/{id_pengguna}/{periode}', [\App\Http\Controllers\AktivitasPengguna::class, 'hitungKwh']);
Route::get('/lihatTransaksiLampuGabungan/{id_pengguna}/{periode}', [\App\Http\Controllers\AktivitasPengguna::class, 'lihatTransaksiLampuGabungan']);
Route::post('/RuanganTransaksiController/', [\App\Http\Controllers\RuanganTransaksiController::class, 'store']);
Route::post('/PinActivityRuanganController/{id_ruangan_transaksi}', [\App\Http\Controllers\PinActivityRuanganController::class, 'store']);
Route::get('/getRuanganWithTransaksi/{id_pengguna}', [\App\Http\Controllers\RuanganController::class, 'getRuanganWithTransaksi']);
Route::get('/coba/', [\App\Http\Controllers\PinActivityRuanganController::class, 'coba']);
Route::post('/CreateTransaksiRuanganSementara/', [\App\Http\Controllers\RuanganTransaksiController::class, 'CreateTransaksiRuanganSementara']);
Route::post('/pinActive/{id_ruangan}/{pin_ruangan}', [\App\Http\Controllers\PinActivityRuanganController::class, 'pinActive']);


Route::post('/updateRemoteTransaksiAC/{AC_id}', [\App\Http\Controllers\TransaksiACController::class, 'updateRemoteTransaksiAC']);









