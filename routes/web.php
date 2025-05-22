<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PSertifikasiController;
use App\Http\Controllers\AuthController;

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

// routes/web.php

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('postlogin', [AuthController::class, 'postlogin'])->name('postlogin');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('home');
    });
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('manage-level')->name('level.')->middleware('authorize:ADM')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('level.index');

        // CRUD routes
        Route::get('/create_ajax', [LevelController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [LevelController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [LevelController::class, 'detail_ajax'])->name('detail_ajax');

        // Import and Export routes
        Route::get('/import', [LevelController::class, 'import'])->name('import');
        Route::post('/import_ajax', [LevelController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [LevelController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [LevelController::class, 'export_pdf'])->name('export_pdf');
    });

    Route::prefix('p_sertifikasi')->name('p_sertifikasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
        Route::get('/', [PSertifikasiController::class, 'index'])->name('index');

        // CRUD routes
        Route::get('/create_ajax', [PSertifikasiController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [PSertifikasiController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [PSertifikasiController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [PSertifikasiController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [PSertifikasiController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [PSertifikasiController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [PSertifikasiController::class, 'detail_ajax'])->name('detail_ajax');
        Route::get('/validasi_ajax/{id}', [PSertifikasiController::class, 'validasi_ajax'])->name('validasi_ajax');
        Route::post('/validasi_ajax/{id}', [PSertifikasiController::class, 'validasi_ajax'])->name('validasi_update');

        // Import and Export routes
        Route::get('/import', [PSertifikasiController::class, 'import'])->name('import');
        Route::post('/import_ajax', [PSertifikasiController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [PSertifikasiController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [PSertifikasiController::class, 'export_pdf'])->name('export_pdf');
    });
});
