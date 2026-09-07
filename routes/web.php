<?php

use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ---------- FRONTEND (subdomain-based locale) ----------
// english.meronews.test  ->  English site
Route::domain('english.' . config('app.domain'))->middleware('locale:en')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
});

// meronews.test  ->  Nepali site (main domain)
Route::domain(config('app.domain'))->middleware('locale:ne')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
});

// ---------- BREEZE (login/register related) ----------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- DASHBOARD REDIRECT (Breeze needs a 'dashboard' named route) ----------
Route::get('/dashboard', function () {
    if (auth()->user() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// ---------- ADMIN ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('news', AdminNewsController::class)->except('show');
    Route::resource('ads', AdController::class)->except('show');
});

require __DIR__.'/auth.php';