<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Boutique
Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::get('/boutique/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{key}', [CartController::class, 'remove'])->name('cart.remove');

// Commande / Checkout
Route::get('/commande', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/succes', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/commande/annulation', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Quiz
Route::get('/quiz/{slug}', [QuizController::class, 'show'])->name('quiz.show');
Route::get('/quiz/{slug}/question/{question}', [QuizController::class, 'question'])->name('quiz.question');
Route::post('/quiz/{slug}/question/{question}', [QuizController::class, 'answer'])->name('quiz.answer');
Route::get('/quiz/{slug}/resultat/{completion}', [QuizController::class, 'result'])->name('quiz.result');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Pages statiques (en dernier pour ne pas capturer les autres routes)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')
    ->where('slug', '^(?!boutique|panier|commande|connexion|inscription|deconnexion|mon-compte|blog|stripe)[a-z0-9-]+$');

// Webhook Stripe (exclure CSRF)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.post');
    Route::get('/inscription', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

// Compte client
Route::prefix('mon-compte')->name('account.')->middleware('auth')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/commandes', [AccountController::class, 'orders'])->name('orders');
    Route::get('/commandes/{order}', [AccountController::class, 'order'])->name('order');
    Route::get('/profil', [AccountController::class, 'editProfile'])->name('profile');
    Route::patch('/profil', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/mot-de-passe', [AccountController::class, 'updatePassword'])->name('password.update');
});
