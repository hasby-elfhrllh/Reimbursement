<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Landing Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

// Dashboard (wajib login & verified)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes yang wajib login
Route::middleware(['auth'])->group(function () {

    /**
     * Categories Routes
     */
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Categories/Index');
        })->name('index');
    });

    /**
     * Reimbursements Routes
     */
    Route::prefix('reimbursements')->name('reimbursements.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Reimbursements/Index');
        })->name('index');
        Route::get('/create', function () {
            return Inertia::render('Reimbursements/Create');
        })->name('create');
    });

    /**
     * Profile Routes
     */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/test-email', function () {
    \Illuminate\Support\Facades\Mail::raw('Ini email test bro!', function ($message) {
        $message->to('hasbycaplank25@gmail.com')
            ->subject('Test Email');
    });

    return 'Email sent!';
});
require __DIR__.'/auth.php';
