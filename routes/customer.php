<?php

use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\ChequeController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\LoanController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\StandingOrderController;
use App\Http\Controllers\Customer\TransactionController;
use App\Http\Controllers\Auth\Customer\AuthenticatedSessionController as CustomerSessionController;
use App\Http\Controllers\Auth\Customer\PasswordResetLinkController    as CustomerPasswordResetController;
use App\Http\Controllers\Auth\Customer\NewPasswordController          as CustomerNewPasswordController;
use App\Http\Controllers\Auth\Customer\RegisterController             as CustomerRegisterController;
use Illuminate\Support\Facades\Route;

// ── Customer Authentication (unauthenticated) ─────────────────────────────────
Route::middleware('guest:customer')
    ->prefix('portal')
    ->name('customer.')
    ->group(function () {
        Route::get('/login',          [CustomerSessionController::class,       'create'])->name('login');
        Route::post('/login',         [CustomerSessionController::class,       'store'])->middleware('throttle:customer-login');

        Route::get('/register',       [CustomerRegisterController::class, 'create'])->name('register');
        Route::post('/register',      [CustomerRegisterController::class, 'store']);

        Route::get('/forgot-password', [CustomerPasswordResetController::class, 'create'])->name('password.request');
        Route::post('/forgot-password',[CustomerPasswordResetController::class, 'store'])->name('password.email');

        Route::get('/reset-password/{token}', [CustomerNewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password',        [CustomerNewPasswordController::class, 'store'])->name('password.store');
    });

// ── Customer Authenticated Area ───────────────────────────────────────────────
Route::middleware(['auth.customer', 'session.timeout:customer'])
    ->prefix('portal')
    ->name('customer.')
    ->group(function () {
        Route::post('/logout', [CustomerSessionController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Accounts + Statement
        Route::get('/accounts',                          [AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/{account}',                [AccountController::class, 'show'])->name('accounts.show');
        Route::get('/accounts/{account}/statement',      [AccountController::class, 'statement'])->name('accounts.statement');

        // Transactions
        Route::get('/transactions',               [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

        // Loans
        Route::get('/loans',               [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/apply',         [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans',              [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}',        [LoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');

        // Standing Orders
        Route::get('/standing-orders',                      [StandingOrderController::class, 'index'])->name('standing-orders.index');
        Route::get('/standing-orders/create',               [StandingOrderController::class, 'create'])->name('standing-orders.create');
        Route::post('/standing-orders',                     [StandingOrderController::class, 'store'])->name('standing-orders.store');
        Route::post('/standing-orders/{order}/pause',       [StandingOrderController::class, 'pause'])->name('standing-orders.pause');
        Route::post('/standing-orders/{order}/resume',      [StandingOrderController::class, 'resume'])->name('standing-orders.resume');
        Route::post('/standing-orders/{order}/cancel',      [StandingOrderController::class, 'cancel'])->name('standing-orders.cancel');

        // Cheques
        Route::get('/cheques', [ChequeController::class, 'index'])->name('cheques.index');

        // Profile + Password
        Route::get('/profile',                [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profile',              [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password',      [ProfileController::class, 'changePassword'])->name('profile.password');
    });
