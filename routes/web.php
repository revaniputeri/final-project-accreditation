<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PSertifikasiController;
use App\Http\Controllers\PKegiatanController;
use App\Http\Controllers\PPrestasiController;
use App\Http\Controllers\POrganisasiController;
use App\Http\Controllers\PPublikasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ValidasiController;

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
    Route::get('lupaPassword',[AuthController::class,'lupaPassword'])->name('lupaPassword');
    Route::POST('verifyDataGuest',[AuthController::class,'verifyDataGuest'])->name('verifyDataGuest');
    Route::GET('/{id}/newPassword',[AuthController::class,'newPassword'])->name('newPassword');
    Route::put('/{id}/updatePassword',[AuthController::class,'updatePassword'])->name('updatePassword');
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
        Route::get('/{id}/confirm_ajax', [LevelController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [LevelController::class, 'detail_ajax'])->name('detail_ajax');

        // Import and Export routes
        Route::get('/import', [LevelController::class, 'import'])->name('import');
        Route::post('/import_ajax', [LevelController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [LevelController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [LevelController::class, 'export_pdf'])->name('export_pdf');
    });

    Route::prefix('manage-user')->name('user.')->middleware('authorize:ADM')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');

        // CRUD routes
        Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [UserController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/confirm_ajax', [UserController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [UserController::class, 'detail_ajax'])->name('detail_ajax');

        // Import and Export routes
        Route::get('/import', [UserController::class, 'import'])->name('import');
        Route::post('/import_ajax', [UserController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [UserController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [UserController::class, 'export_pdf'])->name('export_pdf');
        
        Route::get('/pageProfile',[UserController::class,'pageProfile'])->name('pageProfile');
        Route::get('/{id}/editProfile_ajax', [UserController::class,'editProfile_ajax'])->name('editProfile_ajax');
        Route::PUT('/{id}/updateProfile_ajax', [UserController::class,'updateProfile_ajax'])->name('updateProfile_ajax');
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

    Route::prefix('validasi')->name('validasi.')->middleware('authorize:ADM,VAL')->group(function () {
        Route::GET('/', [ValidasiController::class, 'index'])->name('index');
        Route::POST('/showFile', [ValidasiController::class, 'showFile'])->name('showFile');
        Route::PUT('/valid', [ValidasiController::class,'valid'])->name('valid');
        Route::PUT('/store', [ValidasiController::class, 'store'])->name('store');
    });

    Route::prefix('p_kegiatan')->name('p_kegiatan.')->group(function () {
        Route::get('/', [PKegiatanController::class, 'index'])->name('index');

        // CRUD routes
        Route::get('/create_ajax', [PKegiatanController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/ajax', [PKegiatanController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [PKegiatanController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [PKegiatanController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [PKegiatanController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [PKegiatanController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [PKegiatanController::class, 'detail_ajax'])->name('detail_ajax');
        Route::get('/{id}/validasi_ajax', [PKegiatanController::class, 'validasi_ajax'])->name('validasi_ajax');
        Route::post('/{id}/validasi_ajax', [PKegiatanController::class, 'validasi_ajax'])->name('validasi_ajax.post');
        Route::post('/{id}/validasi_update', [PKegiatanController::class, 'validasi_ajax'])->name('validasi_update');

        // Import and Export routes
        Route::get('/import', [PKegiatanController::class, 'import'])->name('import');
        Route::post('/import_ajax', [PKegiatanController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [PKegiatanController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [PKegiatanController::class, 'export_pdf'])->name('export_pdf');
    });

    Route::prefix('p_prestasi')->name('p_prestasi.')->group(function () {
        Route::get('/', [PPrestasiController::class, 'index'])->name('index');

        // CRUD routes
        Route::get('/create_ajax', [PPrestasiController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/ajax', [PPrestasiController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [PPrestasiController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [PPrestasiController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [PPrestasiController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [PPrestasiController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [PPrestasiController::class, 'detail_ajax'])->name('detail_ajax');
        Route::get('/{id}/validasi_ajax', [PPrestasiController::class, 'validasi_ajax'])->name('validasi_ajax');
        Route::post('/{id}/validasi_ajax', [PPrestasiController::class, 'validasi_ajax'])->name('validasi_ajax.post');

        // Import and Export routes
        Route::get('/import', [PPrestasiController::class, 'import'])->name('import');
        Route::post('/import_ajax', [PPrestasiController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [PPrestasiController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [PPrestasiController::class, 'export_pdf'])->name('export_pdf');
    });

    Route::prefix('p_organisasi')->name('p_organisasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
        Route::get('/', [POrganisasiController::class, 'index'])->name('index');

        // CRUD routes
        Route::get('/create_ajax', [POrganisasiController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [POrganisasiController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [POrganisasiController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [POrganisasiController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [POrganisasiController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [POrganisasiController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [POrganisasiController::class, 'detail_ajax'])->name('detail_ajax');
        Route::get('/validasi_ajax/{id}', [POrganisasiController::class, 'validasi_ajax'])->name('validasi_ajax');
        Route::post('/validasi_ajax/{id}', [POrganisasiController::class, 'validasi_ajax'])->name('validasi_update');

        // Import and Export routes
        Route::get('/import', [POrganisasiController::class, 'import'])->name('import');
        Route::post('/import_ajax', [POrganisasiController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [POrganisasiController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [POrganisasiController::class, 'export_pdf'])->name('export_pdf');
    });

    Route::prefix('p_publikasi')->name('p_publikasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
        Route::get('/', [PPublikasiController::class, 'index'])->name('index');

        // CRUD routes
        Route::get('/create_ajax', [PPublikasiController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [PPublikasiController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [PPublikasiController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [PPublikasiController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/delete_ajax', [PPublikasiController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [PPublikasiController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [PPublikasiController::class, 'detail_ajax'])->name('detail_ajax');
        Route::get('/validasi_ajax/{id}', [PPublikasiController::class, 'validasi_ajax'])->name('validasi_ajax');
        Route::post('/validasi_ajax/{id}', [PPublikasiController::class, 'validasi_ajax'])->name('validasi_update');

        // Import and Export routes
        Route::get('/import', [PPublikasiController::class, 'import'])->name('import');
        Route::post('/import_ajax', [PPublikasiController::class, 'import_ajax'])->name('import_ajax');
        Route::get('/export_excel', [PPublikasiController::class, 'export_excel'])->name('export_excel');
        Route::get('/export_pdf', [PPublikasiController::class, 'export_pdf'])->name('export_pdf');
    });
});
