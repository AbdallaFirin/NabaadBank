<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EndOfDayController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PublicHolidayController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StandingOrderController;
use App\Http\Controllers\Admin\TellerController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BusinessDayController;
use App\Http\Controllers\Admin\ChequeController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\VaultController;
use App\Http\Controllers\Auth\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\PasswordResetLinkController;
use App\Http\Controllers\Auth\Admin\NewPasswordController;
use App\Http\Controllers\Auth\Admin\TwoFactorController;
use Illuminate\Support\Facades\Route;

// ── Staff Authentication (unauthenticated) ────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ── 2FA verification ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor', [TwoFactorController::class, 'verify'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.verify');
    Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');
});

// ── Staff Authenticated Area ──────────────────────────────────────────────────
Route::middleware(['auth', 'two-factor', 'session.timeout:web', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── User Management ───────────────────────────────────────────────────
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status',  [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // ── Roles & Permissions ───────────────────────────────────────────────
        Route::resource('roles', RoleController::class)->except(['show']);

        // ── Customer Management ───────────────────────────────────────────────
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/toggle-status',  [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::post('customers/{customer}/reset-password', [CustomerController::class, 'resetPassword'])->name('customers.reset-password');
        Route::get('customers/{customer}/photo',     [CustomerController::class, 'servePhoto'])->name('customers.photo');
        Route::get('customers/{customer}/signature', [CustomerController::class, 'serveSignature'])->name('customers.signature');

        // ── Account Management ────────────────────────────────────────────────
        Route::get('accounts',                           [AccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/create',                    [AccountController::class, 'create'])->name('accounts.create');
        Route::post('accounts',                          [AccountController::class, 'store'])->name('accounts.store');
        Route::get('accounts/{account}',                 [AccountController::class, 'show'])->name('accounts.show');
        Route::post('accounts/{account}/freeze',         [AccountController::class, 'freeze'])->name('accounts.freeze');
        Route::post('accounts/{account}/unfreeze',       [AccountController::class, 'unfreeze'])->name('accounts.unfreeze');
        Route::post('accounts/{account}/close',          [AccountController::class, 'close'])->name('accounts.close');
        Route::post('accounts/{account}/reactivate',     [AccountController::class, 'reactivate'])->name('accounts.reactivate');

        // ── Loans ─────────────────────────────────────────────────────────────
        Route::get('loans',                          [LoanController::class, 'index'])->name('loans.index');
        Route::get('loans/create',                   [LoanController::class, 'create'])->name('loans.create');
        Route::post('loans',                         [LoanController::class, 'store'])->name('loans.store');
        Route::get('loans/{loan}',                   [LoanController::class, 'show'])->name('loans.show');
        Route::post('loans/{loan}/review',           [LoanController::class, 'review'])->name('loans.review');
        Route::post('loans/{loan}/approve',          [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject',           [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/disburse',         [LoanController::class, 'disburse'])->name('loans.disburse');
        Route::post('loans/{loan}/repay',            [LoanController::class, 'repay'])->name('loans.repay');
        Route::get('loans/accounts/{customer}',      [LoanController::class, 'accounts'])->name('loans.accounts');
        Route::get('loans/eligibility/{customer}',   [LoanController::class, 'eligibility'])->name('loans.eligibility');

        // ── Cheques ───────────────────────────────────────────────────────────
        Route::get('cheques',                          [ChequeController::class, 'index'])->name('cheques.index');
        Route::get('cheques/create',                   [ChequeController::class, 'create'])->name('cheques.create');
        Route::get('cheques/verify',                   [ChequeController::class, 'verify'])->name('cheques.verify');
        Route::post('cheques',                         [ChequeController::class, 'store'])->name('cheques.store');
        Route::post('cheques/{cheque}/encash',         [ChequeController::class, 'encash'])->name('cheques.encash');
        Route::post('cheques/{cheque}/deposit',        [ChequeController::class, 'deposit'])->name('cheques.deposit');
        Route::post('cheques/{cheque}/clear',          [ChequeController::class, 'clear'])->name('cheques.clear');
        Route::post('cheques/{cheque}/cancel',         [ChequeController::class, 'cancel'])->name('cheques.cancel');
        Route::post('cheques/{cheque}/bounce',         [ChequeController::class, 'bounce'])->name('cheques.bounce');
        Route::get('cheques/accounts/{customer}',      [ChequeController::class, 'accounts'])->name('cheques.accounts');
        Route::get('cheques/{cheque}',                 [ChequeController::class, 'show'])->name('cheques.show');

        // ── Business Day ──────────────────────────────────────────────────────
        Route::get('business-day',       [BusinessDayController::class, 'index'])->name('business-day.index');
        Route::post('business-day/open', [BusinessDayController::class, 'open'])->name('business-day.open');
        Route::post('business-day/close',[BusinessDayController::class, 'close'])->name('business-day.close');

        // ── End of Day ────────────────────────────────────────────────────────
        Route::get('eod',              [EndOfDayController::class, 'index'])->name('eod.index');
        Route::get('eod/{run}',        [EndOfDayController::class, 'show'])->name('eod.show');
        Route::post('eod/run',         [EndOfDayController::class, 'run'])->name('eod.run');

        // ── Public Holidays ───────────────────────────────────────────────────
        Route::get('public-holidays',          [PublicHolidayController::class, 'index'])->name('public-holidays.index');
        Route::post('public-holidays',         [PublicHolidayController::class, 'store'])->name('public-holidays.store');
        Route::delete('public-holidays/{holiday}', [PublicHolidayController::class, 'destroy'])->name('public-holidays.destroy');

        // ── Vault ─────────────────────────────────────────────────────────────
        Route::get('vault',              [VaultController::class, 'show'])->name('vault.show');
        Route::post('vault/open',        [VaultController::class, 'open'])->name('vault.open');
        Route::post('vault/close',       [VaultController::class, 'close'])->name('vault.close');
        Route::post('vault/cash-in',     [VaultController::class, 'cashIn'])->name('vault.cash-in');
        Route::post('vault/cash-out',    [VaultController::class, 'cashOut'])->name('vault.cash-out');

        // ── Teller Operations ─────────────────────────────────────────────────
        Route::get('tellers',                                    [TellerController::class, 'index'])->name('tellers.index');
        Route::get('tellers/assign',                             [TellerController::class, 'create'])->name('tellers.create');
        Route::post('tellers',                                   [TellerController::class, 'store'])->name('tellers.store');
        Route::get('tellers/{teller}',                           [TellerController::class, 'show'])->name('tellers.show');
        Route::post('tellers/{teller}/close',                    [TellerController::class, 'close'])->name('tellers.close');
        Route::post('tellers/{teller}/replenish',                [TellerController::class, 'replenish'])->name('tellers.replenish');
        Route::post('tellers/{teller}/return',                   [TellerController::class, 'returnCash'])->name('tellers.return');
        Route::post('tellers/{teller}/transfer',                 [TellerController::class, 'transfer'])->name('tellers.transfer');
        Route::post('tellers/{teller}/request-replenishment',    [TellerController::class, 'requestReplenishment'])->name('tellers.request-replenishment');
        Route::post('tellers/{teller}/approve-replenishment',    [TellerController::class, 'approveReplenishment'])->name('tellers.approve-replenishment');
        Route::post('tellers/{teller}/reject-replenishment',     [TellerController::class, 'rejectReplenishment'])->name('tellers.reject-replenishment');

        // ── Settings ──────────────────────────────────────────────────────────
        Route::get('settings',    [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings',    [SettingsController::class, 'update'])->name('settings.update');

        // ── Audit Logs ────────────────────────────────────────────────────────
        Route::get('audit-logs',           [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        // ── Reports ───────────────────────────────────────────────────────────
        Route::get('reports',                              [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/transactions',                 [ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('reports/teller-summary',               [ReportController::class, 'tellerSummary'])->name('reports.teller-summary');
        Route::get('reports/loans',                        [ReportController::class, 'loans'])->name('reports.loans');
        Route::get('reports/cheques',                      [ReportController::class, 'cheques'])->name('reports.cheques');
        Route::get('reports/receipt/{transaction}',        [ReportController::class, 'receipt'])->name('reports.receipt');
        Route::get('reports/loan-letter/{loan}',           [ReportController::class, 'loanLetter'])->name('reports.loan-letter');
        Route::get('reports/statement/{account}',          [ReportController::class, 'accountStatement'])->name('reports.statement');

        // ── Approvals ─────────────────────────────────────────────────────────
        Route::get('approvals',                              [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('approvals/{transaction}',                [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('approvals/{transaction}/approve',       [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{transaction}/reject',        [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('approvals/{transaction}/escalate',      [ApprovalController::class, 'escalate'])->name('approvals.escalate');

        // ── Transactions ──────────────────────────────────────────────────────
        Route::get('transactions',                          [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/deposit',                  [TransactionController::class, 'depositForm'])->name('transactions.deposit.form');
        Route::post('transactions/deposit',                 [TransactionController::class, 'deposit'])->name('transactions.deposit');
        Route::get('transactions/withdrawal',               [TransactionController::class, 'withdrawalForm'])->name('transactions.withdrawal.form');
        Route::post('transactions/withdrawal',              [TransactionController::class, 'withdrawal'])->name('transactions.withdrawal');
        Route::get('transactions/transfer',                 [TransactionController::class, 'transferForm'])->name('transactions.transfer.form');
        Route::post('transactions/transfer',                [TransactionController::class, 'transfer'])->name('transactions.transfer');
        Route::get('transactions/{transaction}',            [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('transactions/{transaction}/reverse',   [TransactionController::class, 'reverse'])->name('transactions.reverse');

        // ── Standing Orders ───────────────────────────────────────────────────
        Route::get('standing-orders',                       [StandingOrderController::class, 'index'])->name('standing-orders.index');
        Route::get('standing-orders/create',                [StandingOrderController::class, 'create'])->name('standing-orders.create');
        Route::post('standing-orders',                      [StandingOrderController::class, 'store'])->name('standing-orders.store');
        Route::get('standing-orders/{standingOrder}',       [StandingOrderController::class, 'show'])->name('standing-orders.show');
        Route::post('standing-orders/{standingOrder}/pause',  [StandingOrderController::class, 'pause'])->name('standing-orders.pause');
        Route::post('standing-orders/{standingOrder}/resume', [StandingOrderController::class, 'resume'])->name('standing-orders.resume');
        Route::post('standing-orders/{standingOrder}/cancel', [StandingOrderController::class, 'cancel'])->name('standing-orders.cancel');

        // ── KYC Management ────────────────────────────────────────────────────
        Route::get('kyc',                                       [KycController::class, 'index'])->name('kyc.index');
        Route::get('kyc/{kyc}',                                 [KycController::class, 'show'])->name('kyc.show');
        Route::post('kyc/initiate/{customer}',                  [KycController::class, 'initiate'])->name('kyc.initiate');
        Route::post('kyc/{kyc}/upload-document',               [KycController::class, 'uploadDocument'])->name('kyc.upload-document');
        Route::post('kyc/{kyc}/review',                         [KycController::class, 'review'])->name('kyc.review');
        Route::post('kyc/{kyc}/reopen',                         [KycController::class, 'reopen'])->name('kyc.reopen');
        Route::get('kyc/documents/{document}',                  [KycController::class, 'serveDocument'])->name('kyc.document');

        // ── Staff Profile ─────────────────────────────────────────────────────
        Route::get('profile',           [ProfileController::class, 'show'])->name('profile');
        Route::patch('profile',         [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    });

// Logout fallback
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
