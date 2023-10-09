<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\admin\TempImageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use illuminate\Support\Str;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function(){

    Route::group(['middleware' => 'admin/guest'], function(){

        Route::get('/login',[AdminLoginController::class,'adminLogin'])->name('admin/login');
        Route::post('/authenticate',[AdminLoginController::class,'adminAuthenticate'])->name('admin/authenticate');

    });

    Route::group(['middleware' => 'admin/auth'], function(){

        Route::get('/dashboard',[DashboardController::class,'adminDashboard'])->name('admin/dashboard');
        Route::get('/logout',[DashboardController::class,'adminLogout'])->name('admin/logout');
        Route::get('category/create',[CategoryController::class,'create'])->name('category/create');
        Route::post('category/store',[CategoryController::class,'store'])->name('category/store');
        Route::get('category/index',[CategoryController::class,'index'])->name('category/index');
        Route::post('category/tempImage',[TempImageController::class,'create'])->name('category/create');
        Route::get('category/edit/{id}',[CategoryController::class,'edit'])->name('category/edit');
        Route::put('category/update/{id}',[CategoryController::class,'update'])->name('category/update');


        Route::get('/getSlug', function(Request $request){
            $slug = '';

            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }

            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('/getSlug');

    });

});