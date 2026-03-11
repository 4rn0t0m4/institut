<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\BoxtalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\BilanMinceurController;
use App\Http\Controllers\GoogleMerchantFeedController;
use App\Http\Controllers\LegacyRedirectController;
use Illuminate\Support\Facades\Route;

// Redirections legacy WordPress (301)
Route::get('/produit/{slug}', [LegacyRedirectController::class, 'product']);
Route::get('/categorie-produit/{slug}', [LegacyRedirectController::class, 'category']);
Route::redirect('/remboursements_retours', '/remboursements-retours', 301);

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Flux Google Shopping (produits hors bijoux)
Route::get('/flux-google-shopping.xml', [GoogleMerchantFeedController::class, 'index'])->name('google.merchant.feed');

// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Boutique
Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::post('/boutique/{product}/alerte-stock', [ShopController::class, 'stockNotify'])->name('shop.stock-notify');
Route::post('/boutique/{product}/avis', [ShopController::class, 'storeReview'])->name('shop.review.store');
Route::get('/boutique/{parent}/{child}/{product}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/boutique/{parent}/{child?}', [ShopController::class, 'categoryOrProduct'])->name('shop.category');

// Panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::get('/panier/mini', [CartController::class, 'miniCart'])->name('cart.mini');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{key}', [CartController::class, 'remove'])->name('cart.remove');

// Commande / Checkout
Route::get('/commande', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/succes', [CheckoutController::class, 'success'])->name('checkout.success');

// Quiz
Route::get('/quiz/{slug}', [QuizController::class, 'show'])->name('quiz.show');
Route::get('/quiz/{slug}/question/{question}', [QuizController::class, 'question'])->name('quiz.question');
Route::post('/quiz/{slug}/question/{question}', [QuizController::class, 'answer'])->name('quiz.answer');
Route::get('/quiz/{slug}/resultat/{completion}', [QuizController::class, 'result'])->name('quiz.result');

// Bilan Minceur (avant le wildcard pages)
Route::get('/amincissement/bilan-minceur-personnalise', [BilanMinceurController::class, 'show'])->name('bilan-minceur.show');
Route::post('/amincissement/bilan-minceur-personnalise', [BilanMinceurController::class, 'submit'])->name('bilan-minceur.submit');

// Pages statiques (en dernier pour ne pas capturer les autres routes)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')
    ->where('slug', '^(?!boutique|panier|commande|connexion|inscription|deconnexion|mon-compte|stripe|admin|api|mot-de-passe-oublie|reinitialiser-mot-de-passe|sitemap\.xml)[a-z0-9-]+(/[a-z0-9-]+)*$');

// Boxtal API
Route::get('/api/boxtal/map-token', [BoxtalController::class, 'mapToken'])->name('boxtal.map-token');
Route::post('/api/boxtal/parcel-points', [BoxtalController::class, 'searchParcelPoints'])->name('boxtal.parcel-points');

// Webhook Stripe (exclure CSRF)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/inscription', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
    Route::get('/mot-de-passe-oublie', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reinitialiser-mot-de-passe/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:3,1');
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
    Route::get('/coordonnees', [AccountController::class, 'editAddress'])->name('address');
    Route::patch('/coordonnees', [AccountController::class, 'updateAddress'])->name('address.update');
});
