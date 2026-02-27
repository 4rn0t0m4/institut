<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Accueil
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Boutique
Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::get('/boutique/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{key}', [CartController::class, 'remove'])->name('cart.remove');
