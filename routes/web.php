<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// General Routes
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('homepage');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Auth::routes();

// Shop Routes
Route::get('/shop/{slug?}', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/tag/{slug?}', [\App\Http\Controllers\ShopController::class, 'tag'])->name('shop.tag');
Route::get('/product/{product:slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

// React Routes
Route::get('products/{slug?}', [\App\Http\Controllers\ShopController::class, 'getProducts']);
Route::get('products', [\App\Http\Controllers\HomeController::class, 'getProducts']);
Route::get('product-detail/{product:slug}', [\App\Http\Controllers\ProductController::class, 'getProductDetail']);
Route::post('carts', [\App\Http\Controllers\CartController::class, 'store']);
Route::get('carts', [\App\Http\Controllers\CartController::class, 'showCart']);

// Ongkir API Routes
Route::prefix('api')->group(function () {
    Route::get('provinces', [\App\Http\Controllers\OngkirController::class, 'getProvinces']);
    Route::get('cities', [\App\Http\Controllers\OngkirController::class, 'cities']);
    Route::get('shipping-cost', [\App\Http\Controllers\OngkirController::class, 'shippingCost']);
    Route::post('set-shipping', [\App\Http\Controllers\OngkirController::class, 'setShipping']);
    Route::post('checkout', [\App\Http\Controllers\OrderController::class, 'checkout']);
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index']);
});

// Checkout Routes
Route::middleware('auth')->group(function () {

    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

    // Cart Routes
    Route::resource('/cart', \App\Http\Controllers\CartController::class)->except(['store', 'show']);

    // Admin Routes
    Route::middleware(['isAdmin'])->prefix('admin')->as('admin.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::post('categories/images', [\App\Http\Controllers\Admin\CategoryController::class, 'storeImage'])->name('categories.storeImage');

        // Tags
        Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);

        // Products
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::post('products/images', [\App\Http\Controllers\Admin\ProductController::class, 'storeImage'])->name('products.storeImage');
    });
});
