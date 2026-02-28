<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::resource('products', ProductController::class)->names('admin.products');
Route::resource('categories', ProductCategoryController::class)->names('admin.categories');
Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update'])->names('admin.orders');
Route::resource('pages', PageController::class)->names('admin.pages');
