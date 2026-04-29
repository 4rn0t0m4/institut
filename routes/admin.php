<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::resource('products', ProductController::class)->names('admin.products');
Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
Route::resource('categories', ProductCategoryController::class)->names('admin.categories');
Route::resource('brands', BrandController::class)->except(['show'])->names('admin.brands');
Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update', 'destroy'])->names('admin.orders');
Route::post('orders/{order}/resend-emails', [OrderController::class, 'resendEmails'])->name('admin.orders.resend-emails');
Route::post('orders/{order}/create-shipment', [OrderController::class, 'createShipment'])->name('admin.orders.create-shipment');
Route::get('orders/{order}/label', [OrderController::class, 'label'])->name('admin.orders.label');
Route::delete('orders/{order}/reset-shipment', [OrderController::class, 'resetShipment'])->name('admin.orders.reset-shipment');
Route::resource('customers', CustomerController::class)->only(['index', 'show'])->names('admin.customers');
Route::resource('pages', PageController::class)->names('admin.pages');
Route::resource('discounts', DiscountController::class)->except(['show'])->names('admin.discounts');
Route::resource('tags', ProductTagController::class)->except(['show'])->names('admin.tags');

Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject');
Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

Route::get('shipping', [ShippingController::class, 'index'])->name('admin.shipping.index');
Route::put('shipping', [ShippingController::class, 'update'])->name('admin.shipping.update');

Route::get('settings', [SettingController::class, 'index'])->name('admin.settings.index');
Route::put('settings', [SettingController::class, 'update'])->name('admin.settings.update');

Route::get('announcement', [AnnouncementController::class, 'index'])->name('admin.announcement.index');
Route::put('announcement', [AnnouncementController::class, 'update'])->name('admin.announcement.update');

Route::get('exports', [ExportController::class, 'index'])->name('admin.exports.index');
Route::get('exports/excel', [ExportController::class, 'excel'])->name('admin.exports.excel');

Route::post('editor-upload', [EditorUploadController::class, 'upload'])->name('admin.editor.upload');

Route::get('boxtal-subscriptions', [\App\Http\Controllers\Admin\BoxtalSubscriptionController::class, 'index'])->name('admin.boxtal-subscriptions.index');
Route::post('boxtal-subscriptions', [\App\Http\Controllers\Admin\BoxtalSubscriptionController::class, 'store'])->name('admin.boxtal-subscriptions.store');
Route::post('boxtal-subscriptions/test', [\App\Http\Controllers\Admin\BoxtalSubscriptionController::class, 'test'])->name('admin.boxtal-subscriptions.test');
Route::delete('boxtal-subscriptions/{id}', [\App\Http\Controllers\Admin\BoxtalSubscriptionController::class, 'destroy'])->name('admin.boxtal-subscriptions.destroy');
Route::get('boxtal-offers', [\App\Http\Controllers\Admin\BoxtalSubscriptionController::class, 'offers'])->name('admin.boxtal-offers');
