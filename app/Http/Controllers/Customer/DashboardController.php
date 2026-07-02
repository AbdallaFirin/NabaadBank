<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoanRepaymentSchedule;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $customer = Auth::guard('customer')->user();
        $customer->load('activeAccounts');

        $accountIds = $customer->activeAccounts->pluck('id');

        $recentTransactions = Transaction::with('account')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $totalBalance = $customer->activeAccounts->sum('balance');

        // Next unpaid instalment across all active/overdue loans
        $activeLoanIds = $customer->loans()
            ->whereIn('status', ['active', 'overdue'])
            ->pluck('id');

        $nextDue = LoanRepaymentSchedule::whereIn('loan_id', $activeLoanIds)
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->with('loan')
            ->first();

        // Accounts where balance is at or below minimum_balance
        $lowBalanceAccounts = $customer->activeAccounts->filter(function ($acc) {
            $minBalance = (float) ($acc->minimum_balance ?? 0);
            $threshold  = max($minBalance, 50); // warn if at/below $50 or minimum_balance
            return (float) $acc->balance <= $threshold;
        })->values();

        $stats = [
            'total_balance'   => $totalBalance,
            'account_count'   => $customer->activeAccounts->count(),
            'active_loans'    => $activeLoanIds->count(),
            'pending_cheques' => 0,
        ];

        return Inertia::render('Customer/Dashboard', [
            'customer'             => $customer,
            'accounts'             => $customer->activeAccounts,
            'recent_transactions'  => $recentTransactions,
            'stats'                => $stats,
            'next_due'             => $nextDue,
            'low_balance_accounts' => $lowBalanceAccounts,
        ]);
    }
}
