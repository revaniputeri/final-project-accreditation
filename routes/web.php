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
use App\Http\Controllers\PPenelitianController;
use App\Http\Controllers\PKaryaBukuController;
use App\Http\Controllers\PHKIController;
use App\Http\Controllers\PPengabdianController;
use App\Http\Controllers\PProfesiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\DokumenKriteriaController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\DashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\DokumenAkhirController;

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

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('landing_page.index');
});

Route::get('/kriteria/{no_kriteria}', [KriteriaController::class, 'showDokumenPendukung'])->name('kriteria.showDokumenPendukung');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('postlogin', [AuthController::class, 'postlogin'])->name('postlogin');
    Route::get('lupaPassword', [AuthController::class, 'lupaPassword'])->name('lupaPassword');
    Route::POST('verifyDataGuest', [AuthController::class, 'verifyDataGuest'])->name('verifyDataGuest');
    Route::GET('/{id}/newPassword', [AuthController::class, 'newPassword'])->name('newPassword');
    Route::put('/{id}/updatePassword', [AuthController::class, 'updatePassword'])->name('updatePassword');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk manage-level
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

    Route::prefix('manage-profile')->name('profile.')->middleware('auth')->group(function () {
        Route::get('/pageProfile', [UserController::class, 'pageProfile'])->name('pageProfile');
        Route::get('/{id}/editProfile_ajax', [UserController::class, 'editProfile_ajax'])->name('editProfile_ajax');
        Route::PUT('/{id}/updateProfile_ajax', [UserController::class, 'updateProfile_ajax'])->name('updateProfile_ajax');
        Route::get('/editPhoto_ajax', [UserController::class, 'editPhoto_ajax'])->name('editPhoto_ajax');
        Route::Post('/storePhoto_ajax', [UserController::class, 'storePhoto_ajax'])->name('storePhoto_ajax');
    });

    // Route untuk manage-user
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
    });

    // Route untuk manage-kriteria
    Route::prefix('manage-kriteria')->group(function () {
        Route::get('/', [KriteriaController::class, 'index'])->name('kriteria.index');

        // CRUD routes
        Route::get('/create_ajax', [KriteriaController::class, 'create_ajax'])->name('kriteria.create_ajax');
        Route::post('/store_ajax', [KriteriaController::class, 'store_ajax'])->name('kriteria.store_ajax');
        Route::get('/edit_ajax/{no_kriteria}/{id_user}', [KriteriaController::class, 'edit_ajax'])->name('kriteria.edit_ajax');
        Route::post('/update_ajax/{no_kriteria}/{id_user}', [KriteriaController::class, 'update_ajax'])->name('kriteria.update_ajax');
        Route::get('/detail_ajax/{no_kriteria}/{id_user}', [KriteriaController::class, 'detail_ajax'])->name('kriteria.detail_ajax');
        Route::get('/confirm_ajax/{no_kriteria}/{id_user}', [KriteriaController::class, 'confirm_ajax'])->name('kriteria.confirm_ajax');
        Route::delete('/delete_ajax/{no_kriteria}/{id_user}', [KriteriaController::class, 'delete_ajax'])->name('kriteria.delete_ajax');

        // Import and Export routes
        Route::get('/export_excel', [KriteriaController::class, 'export_excel'])->name('kriteria.export_excel');
        Route::get('/export_pdf', [KriteriaController::class, 'export_pdf'])->name('kriteria.export_pdf');

        // Get last number and users
        Route::get('/get-last-number', [KriteriaController::class, 'getLastNumber'])->name('kriteria.get_last_number');
        Route::get('/get-users', [KriteriaController::class, 'getUsers'])->name('kriteria.get_users');
    });

    // Route untuk validasi
    Route::prefix('validasi')->name('validasi.')->middleware('authorize:VAL')->group(function () {
        Route::GET('/', [ValidasiController::class, 'index'])->name('index');
        Route::POST('/showFile', [ValidasiController::class, 'showFile'])->name('showFile');
        Route::PUT('/valid', [ValidasiController::class, 'valid'])->name('valid');
        Route::PUT('/store', [ValidasiController::class, 'store'])->name('store');
        Route::post('/getDokumenInfo', [ValidasiController::class, 'getDokumenInfo'])->name('getDokumenInfo');
    });

    // Route untuk dokumen kriteria
    Route::prefix('dokumen_kriteria')->name('dokumen_kriteria.')->middleware('authorize:ANG')->group(function () {
        Route::get('/', [DokumenKriteriaController::class, 'index'])->name('index');
        Route::put('/update/{id}', [DokumenKriteriaController::class, 'update'])->name('update');
        Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');
        Route::get('/create_ajax', [DokumenKriteriaController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [DokumenKriteriaController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}/edit_ajax', [DokumenKriteriaController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [DokumenKriteriaController::class, 'update_ajax'])->name('update_ajax');
        Route::get('/{id}/confirm_ajax', [DokumenKriteriaController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete_ajax', [DokumenKriteriaController::class, 'delete_ajax'])->name('delete_ajax');
        Route::get('/{id}/detail_ajax', [DokumenKriteriaController::class, 'detail_ajax'])->name('detail_ajax');
        Route::post('/{id}/copy_path', [DokumenKriteriaController::class, 'copyPath'])->name('copy_path');
    });

    // Route gabungan portofolio
    Route::prefix('portofolio')->name('portofolio.')->group(function () {

        // Route Portofolio Sertifikasi
        Route::prefix('sertifikasi')->name('sertifikasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
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

            Route::get('/chart1',[PSertifikasiController::class,'chart1'])->name('chart1');
            Route::get('/chart2',[PSertifikasiController::class,'chart2'])->name('chart2');

        });

        // Route Portofolio Kegiatan
        Route::prefix('kegiatan')->name('kegiatan.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
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

            // Charts
            Route::get('/chart1',[PKegiatanController::class,'chart1'])->name('chart1');
           Route::get('/chart2',[PKegiatanController::class,'chart2'])->name('chart2');

        });

        // Route Portofolio Prestasi
        Route::prefix('prestasi')->name('prestasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
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
            Route::post('/{id}/validasi_ajax', [PPrestasiController::class, 'validasi_ajax'])->name('validasi_update');

            // Import and Export routes
            Route::get('/import', [PPrestasiController::class, 'import'])->name('import');
            Route::post('/import_ajax', [PPrestasiController::class, 'import_ajax'])->name('import_ajax');
            Route::get('/export_excel', [PPrestasiController::class, 'export_excel'])->name('export_excel');
            Route::get('/export_pdf', [PPrestasiController::class, 'export_pdf'])->name('export_pdf');
        });

        // Route Portofolio Organisasi
        Route::prefix('organisasi')->name('organisasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
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

            // Chart
            Route::get('/chart1',[POrganisasiController::class,'chart1'])->name('chart1');
            Route::get('/chart2',[POrganisasiController::class,'chart2'])->name('chart2');
            Route::get('/chart3',[POrganisasiController::class,'chart3'])->name('chart3');
        });

        // Route Portofolio Publikasi
        Route::prefix('publikasi')->name('publikasi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
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

            Route::get('/chart1',[PPublikasiController::class,'chart1'])->name('chart1');
            Route::get('/chart2',[PPublikasiController::class,'chart2'])->name('chart2');
        });

        // Route Portofolio Penelitian
        Route::prefix('penelitian')->name('penelitian.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
            Route::get('/', [PPenelitianController::class, 'index'])->name('index');

            // CRUD dengan AJAX
            Route::get('/create_ajax', [PPenelitianController::class, 'create_ajax'])->name('create_ajax');
            Route::post('/store_ajax', [PPenelitianController::class, 'store_ajax'])->name('store_ajax');
            Route::get('/{id}/edit_ajax', [PPenelitianController::class, 'edit_ajax'])->name('edit_ajax');
            Route::put('/{id}/update_ajax', [PPenelitianController::class, 'update_ajax'])->name('update_ajax');
            Route::get('/{id}/delete_ajax', [PPenelitianController::class, 'confirm_ajax'])->name('confirm_ajax');
            Route::delete('/{id}/delete_ajax', [PPenelitianController::class, 'delete_ajax'])->name('delete_ajax');
            Route::get('/{id}/detail_ajax', [PPenelitianController::class, 'detail_ajax'])->name('detail_ajax');
            Route::get('/validasi_ajax/{id}', [PPenelitianController::class, 'validasi_ajax'])->name('validasi_ajax');
            Route::post('/validasi_ajax/{id}', [PPenelitianController::class, 'validasi_ajax'])->name('validasi_update');

            // Import & Export
            Route::get('/import', [PPenelitianController::class, 'import'])->name('import');
            Route::post('/import_ajax', [PPenelitianController::class, 'import_ajax'])->name('import_ajax');
            Route::get('/export_excel', [PPenelitianController::class, 'export_excel'])->name('export_excel');
            Route::get('/export_pdf', [PPenelitianController::class, 'export_pdf'])->name('export_pdf');
        });

        // Route Portofolio Karya Buku
        Route::prefix('karya-buku')->name('karya_buku.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
            Route::get('/', [PKaryaBukuController::class, 'index'])->name('index');

            // CRUD dengan AJAX
            Route::get('/create_ajax', [PKaryaBukuController::class, 'create_ajax'])->name('create_ajax');
            Route::post('/store_ajax', [PKaryaBukuController::class, 'store_ajax'])->name('store_ajax');
            Route::get('/{id}/edit_ajax', [PKaryaBukuController::class, 'edit_ajax'])->name('edit_ajax');
            Route::put('/{id}/update_ajax', [PKaryaBukuController::class, 'update_ajax'])->name('update_ajax');
            Route::get('/{id}/delete_ajax', [PKaryaBukuController::class, 'confirm_ajax'])->name('confirm_ajax');
            Route::delete('/{id}/delete_ajax', [PKaryaBukuController::class, 'delete_ajax'])->name('delete_ajax');
            Route::get('/{id}/detail_ajax', [PKaryaBukuController::class, 'detail_ajax'])->name('detail_ajax');
            Route::get('/validasi_ajax/{id}', [PKaryaBukuController::class, 'validasi_ajax'])->name('validasi_ajax');
            Route::post('/validasi_ajax/{id}', [PKaryaBukuController::class, 'validasi_ajax'])->name('validasi_update');

            // Import & Export
            Route::get('/import', [PKaryaBukuController::class, 'import'])->name('import');
            Route::post('/import_ajax', [PKaryaBukuController::class, 'import_ajax'])->name('import_ajax');
            Route::get('/export_excel', [PKaryaBukuController::class, 'export_excel'])->name('export_excel');
            Route::get('/export_pdf', [PKaryaBukuController::class, 'export_pdf'])->name('export_pdf');
        });

        // Route Portofolio HKI
        Route::prefix('hki')->name('hki.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
            Route::get('/', [PHKIController::class, 'index'])->name('index');

            // CRUD routes
            Route::get('/create_ajax', [PHKIController::class, 'create_ajax'])->name('create_ajax');
            Route::post('/store_ajax', [PHKIController::class, 'store_ajax'])->name('store_ajax');
            Route::get('/{id}/edit_ajax', [PHKIController::class, 'edit_ajax'])->name('edit_ajax');
            Route::put('/{id}/update_ajax', [PHKIController::class, 'update_ajax'])->name('update_ajax');
            Route::get('/{id}/delete_ajax', [PHKIController::class, 'confirm_ajax'])->name('confirm_ajax');
            Route::delete('/{id}/delete_ajax', [PHKIController::class, 'delete_ajax'])->name('delete_ajax');
            Route::get('/{id}/detail_ajax', [PHKIController::class, 'detail_ajax'])->name('detail_ajax');
            Route::get('/validasi_ajax/{id}', [PHKIController::class, 'validasi_ajax'])->name('validasi_ajax');
            Route::post('/validasi_ajax/{id}', [PHKIController::class, 'validasi_ajax'])->name('validasi_update');

            // Import and Export routes
            Route::get('/import', [PHKIController::class, 'import'])->name('import');
            Route::post('/import_ajax', [PHKIController::class, 'import_ajax'])->name('import_ajax');
            Route::get('/export_excel', [PHKIController::class, 'export_excel'])->name('export_excel');
            Route::get('/export_pdf', [PHKIController::class, 'export_pdf'])->name('export_pdf');
        });

        // Route Portofolio Pengabdian Masyarakat
        Route::prefix('pengabdian')->name('pengabdian.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
            Route::middleware('authorize:DOS,ANG,ADM')->group(function () {
                Route::get('/', [PPengabdianController::class, 'index'])->name('index');
                Route::get('/{id}/detail_ajax', [PPengabdianController::class, 'detail_ajax'])->name('detail_ajax');
                Route::get('/export_excel', [PPengabdianController::class, 'export_excel'])->name('export_excel');
                Route::get('/export_pdf', [PPengabdianController::class, 'export_pdf'])->name('export_pdf');
            });
            Route::middleware('authorize:DOS,ADM')->group(function () {
                Route::get('/create_ajax', [PPengabdianController::class, 'create_ajax'])->name('create_ajax');
                Route::post('/store_ajax', [PPengabdianController::class, 'store_ajax'])->name('store_ajax');
                Route::get('/{id}/edit_ajax', [PPengabdianController::class, 'edit_ajax'])->name('edit_ajax');
                Route::put('/{id}/update_ajax', [PPengabdianController::class, 'update_ajax'])->name('update_ajax');
                Route::get('/{id}/delete_ajax', [PPengabdianController::class, 'confirm_ajax'])->name('confirm_ajax');
                Route::delete('/{id}/delete_ajax', [PPengabdianController::class, 'delete_ajax'])->name('delete_ajax');
                Route::get('/import', [PPengabdianController::class, 'import'])->name('import');
                Route::post('/import_ajax', [PPengabdianController::class, 'import_ajax'])->name('import_ajax');
            });
            Route::middleware('authorize:DOS')->group(function () {
                Route::get('/validasi_ajax/{id}', [PPengabdianController::class, 'validasi_ajax'])->name('validasi_ajax');
                Route::post('/validasi_ajax/{id}', [PPengabdianController::class, 'validasi_ajax'])->name('validasi_update');
            });
        });

        // Route Portofolio Profesi
        Route::prefix('profesi')->name('profesi.')->middleware('authorize:DOS,ANG,ADM')->group(function () {
            Route::get('/', [PProfesiController::class, 'index'])->name('index');

            // CRUD routes
            Route::get('/create_ajax', [PProfesiController::class, 'create_ajax'])->name('create_ajax');
            Route::post('/store_ajax', [PProfesiController::class, 'store_ajax'])->name('store_ajax');
            Route::get('/{id}/edit_ajax', [PProfesiController::class, 'edit_ajax'])->name('edit_ajax');
            Route::put('/{id}/update_ajax', [PProfesiController::class, 'update_ajax'])->name('update_ajax');
            Route::get('/{id}/delete_ajax', [PProfesiController::class, 'confirm_ajax'])->name('confirm_ajax');
            Route::delete('/{id}/delete_ajax', [PProfesiController::class, 'delete_ajax'])->name('delete_ajax');
            Route::get('/{id}/detail_ajax', [PProfesiController::class, 'detail_ajax'])->name('detail_ajax');
            Route::get('/validasi_ajax/{id}', [PProfesiController::class, 'validasi_ajax'])->name('validasi_ajax');
            Route::post('/validasi_ajax/{id}', [PProfesiController::class, 'validasi_ajax'])->name('validasi_update');

            // Import and Export routes
            Route::get('/import', [PProfesiController::class, 'import'])->name('import');
            Route::post('/import_ajax', [PProfesiController::class, 'import_ajax'])->name('import_ajax');
            Route::get('/export_excel', [PProfesiController::class, 'export_excel'])->name('export_excel');
            Route::get('/export_pdf', [PProfesiController::class, 'export_pdf'])->name('export_pdf');
        });
    });

    // Route dashboard
    Route::prefix('chart')->name('chart.')->middleware('authorize:ADM,VAL,ANG')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/more-info', [DashboardController::class, 'moreInfo'])->name('moreInfo');

    });

    // Route untuk dokumen akhir
    Route::prefix('dokumen-akhir')->name('dokumen_akhir.')->middleware('authorize:VAL,DIR')->group(function () {
        Route::get('/', [DokumenAkhirController::class, 'index'])->name('index');
    });
});
