<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('storefront.home');
Route::get('/about', [StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/products', [StorefrontController::class, 'products'])->name('storefront.products');
Route::get('/services', [StorefrontController::class, 'services'])->name('storefront.services');
Route::get('/gallery', [StorefrontController::class, 'gallery'])->name('storefront.gallery');
Route::get('/gallery/{album}', [StorefrontController::class, 'galleryAlbum'])->name('storefront.gallery.album');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('storefront.shop');
Route::get('/shop/{product}', [StorefrontController::class, 'product'])->name('storefront.product');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('storefront.contact');
Route::post('/contact', [StorefrontController::class, 'submitContact'])->name('storefront.contact.submit');

Route::get('/cart', [StorefrontController::class, 'cart'])->name('storefront.cart');
Route::post('/cart/{product}', [StorefrontController::class, 'addToCart'])->name('storefront.cart.add');
Route::patch('/cart/{product}', [StorefrontController::class, 'updateCart'])->name('storefront.cart.update');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
Route::post('/checkout', [StorefrontController::class, 'placeOrder'])->name('storefront.checkout.place');
Route::get('/checkout/thank-you', [StorefrontController::class, 'checkoutThankYou'])->name('storefront.checkout.thank-you');

Route::get('/wishlist', [StorefrontController::class, 'wishlist'])->name('storefront.wishlist');
Route::post('/wishlist/{product}', [StorefrontController::class, 'addToWishlist'])->name('storefront.wishlist.add');

require __DIR__.'/admin.php';
