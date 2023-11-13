<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\front\AuthController;
use App\Http\Controllers\front\CartController;
use App\Http\Controllers\front\FrontController;
use App\Http\Controllers\front\ShopController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin/guest'], function () {

        Route::get('/login', [AdminLoginController::class, 'adminLogin'])->name('admin/login');
        Route::post('/authenticate', [AdminLoginController::class, 'adminAuthenticate'])->name('admin/authenticate');
    });

    Route::group(['middleware' => 'admin/auth'], function () {

        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin/dashboard');
        Route::get('/logout', [DashboardController::class, 'adminLogout'])->name('admin/logout');

        //Category Routes

        Route::get('category/create', [CategoryController::class, 'create'])->name('category/create');
        Route::post('category/store', [CategoryController::class, 'store'])->name('category/store');
        Route::get('category/index', [CategoryController::class, 'index'])->name('category/index');
        Route::post('category/tempImage', [TempImageController::class, 'create'])->name('category/create');
        Route::get('category/edit/{id}', [CategoryController::class, 'edit'])->name('category/edit');
        Route::put('category/update/{id}', [CategoryController::class, 'update'])->name('category/update');
        Route::get('category/destroy/{id}', [CategoryController::class, 'destroy'])->name('category/destroy');

        //Sub Category Route

        Route::get('sub-category/create', [SubCategoryController::class, 'create'])->name('sub-category/create');
        Route::post('sub-category/store', [SubCategoryController::class, 'store'])->name('sub-category/store');
        Route::get('sub-category/index', [SubCategoryController::class, 'index'])->name('sub-category/index');
        Route::get('sub-category/edit/{id}', [SubCategoryController::class, 'edit'])->name('sub-category/edit');
        Route::put('sub-category/update/{id}', [SubCategoryController::class, 'update'])->name('sub-category/update');
        Route::get('sub-category/destroy/{id}', [SubCategoryController::class, 'destroy'])->name('sub-category/destroy');

        //Brands Route

        Route::get('brands/create', [BrandsController::class, 'create'])->name('brands/create');
        Route::post('brands/store', [BrandsController::class, 'store'])->name('brands/store');
        Route::get('brands/index', [BrandsController::class, 'index'])->name('brands/index');
        Route::get('brands/edit/{id}', [BrandsController::class, 'edit'])->name('brands/edit');
        Route::put('brands/update/{id}', [BrandsController::class, 'update'])->name('brands/update');
        Route::get('brands/destroy/{id}', [BrandsController::class, 'destroy'])->name('brands/destroy');

        //Products Route

        Route::get('products/create', [ProductController::class, 'create'])->name('products/create');
        Route::post('products/store', [ProductController::class, 'store'])->name('products/store');
        Route::get('products/index', [ProductController::class, 'index'])->name('products/index');
        Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('products/edit');
        Route::put('products/update/{id}', [ProductController::class, 'update'])->name('products/update');
        Route::get('products/destroy/{id}', [ProductController::class, 'destroy'])->name('products/destroy');
        Route::get('product-subcategories', [ProductSubCategoryController::class, 'index'])->name('products-subcategories/index');

        //Shipping Route

        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping/create');
        Route::post('shipping/store', [ShippingController::class, 'store'])->name('shipping/store');
        Route::get('shipping/destroy/{id}', [ShippingController::class, 'destroy'])->name('shipping/destroy');

        //Users Route

        Route::get('users/index', [UserController::class, 'index'])->name('users/index');
        Route::get('users/create', [UserController::class, 'create'])->name('users/create');
        Route::get('users/destroy/{id}', [UserController::class, 'destroy'])->name('users/destroy');

        Route::get('/getSlug', function (Request $request) {
            $slug = '';

            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }

            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('/getSlug');
    });
});


//Front Routes

Route::get('/', [FrontController::class, 'index'])->name('front/index');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front/shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front/product');

//Cart Route

Route::get('/cart', [CartController::class, 'cart'])->name('front/cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front/addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front/updateCart');
Route::post('/delete-cart', [CartController::class, 'deleteItem'])->name('front/deleteItem');

Route::group(['prefix' => 'account'], function () {

    Route::group(['middleware' => 'guest'], function () {

        Route::get('/register', [AuthController::class, 'register'])->name('account/register');
        Route::get('/login', [AuthController::class, 'login'])->name('account/login');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account/processRegister');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account/authenticate');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('account/profile');
        Route::get('/logout', [AuthController::class, 'logout'])->name('account/logout');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('account/checkout');
        Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('account/processCheckout');
        Route::get('/thankYou', [CartController::class, 'thankYou'])->name('account/thankYou');
    });
});
