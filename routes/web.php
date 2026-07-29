<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerControlller;
use App\Http\Controllers\EslonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\McuController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Setting;
use App\Http\Controllers\Sp3Controller;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubLayananController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserManagementController;
use App\Models\Simrs\BillingSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use App\Models\Simrs\RegSimrs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

/** for side bar menu active */
// function set_active($route)
// {
//     if (is_array($route)) {
//         return in_array(Request::path(), $route) ? 'active' : '';
//     }
//     return Request::path() == $route ? 'active' : '';
// }

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', function () {
        return view('home');
    });
});
Route::get('/test', function () {
    return RegSimrs::select(['no_mr', 'reg_no', 'nama', 'tanggal_registrasi'])->whereRaw("DATE(tanggal_registrasi) = ?", [now()->format('Y-m-d')])->count();
});

Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Auth'], function () {
    // ----------------------------login ------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->middleware('guest')->name('login');
        Route::post('/login', 'authenticate')->middleware('guest');
        Route::get('/logout', 'logout')->middleware('auth')->name('logout-user');
        Route::post('change/password', 'changePassword')->middleware('auth')->name('change/password');
    });
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::middleware('auth')->group(function () {
        // -------------------------- main dashboard ----------------------//
        Route::controller(HomeController::class)->group(function () {
            Route::get('/home', 'index')->name('home');
            Route::get('user/profile/page', 'userProfile')->name('user/profile/page');
            Route::get('teacher/dashboard', 'teacherDashboardIndex')->name('teacher/dashboard');
            Route::get('student/dashboard', 'studentDashboardIndex')->name('student/dashboard');
        });

        // ----------------------------- user controller ---------------------//
        Route::controller(UserManagementController::class)->group(function () {
            Route::get('list/users', 'index')->name('list/users');
            Route::post('change/password', 'changePassword')->name('change/password');
            Route::get('view/user/edit/{id}', 'userView');
            Route::get('user/create', 'create')->name('user/create');
            Route::post('user/store', 'store')->name('user/store');
            Route::post('user/update', 'userUpdate')->name('user/update');
            Route::post('user/delete', 'userDelete')->name('user/delete');
            Route::get('get-users-data', 'getUsersData')->name('get-users-data');
            /** get all data users */
        });

        Route::controller(CustomerControlller::class)->group(function () {
            Route::prefix('kasir')->group(function () {
                Route::get('/customers', 'index')->name('list-customers');
                Route::get('/reload-customers', 'reload')->name('reload-customers');
                Route::get('/data-customers', 'getDataCustomers')->name('get-data-customers');
                Route::post('/customers/update', 'update')->name('update-customer');
                Route::get('/struk/{id}', 'cetak')->name('struk.cetak');
            });
        });

        Route::get('/laporan/invoice/export', [ReportController::class, 'export'])->name('laporan.invoice.export');

        // ------------------------ setting -------------------------------//
        Route::controller(Setting::class)->group(function () {
            Route::get('setting/page', 'index')->name('setting/page');
        });
    });
});
