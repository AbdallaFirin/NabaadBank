<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = today();

        $pendingApprovals = Transaction::with(['account.customer', 'processedBy'])
            ->where('status', 'pending')
            ->where('requires_approval', true)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'customers'          => Customer::where('status', 'active')->count(),
                'active_accounts'    => Account::where('status', 'active')->count(),
                'today_transactions' => Transaction::whereDate('created_at', $today)->count(),
                'active_loans'       => Loan::whereIn('status', ['active', 'overdue'])->count(),
            ],
            'pending_approvals'       => $pendingApprovals,
            'pending_approvals_count' => Transaction::where('status', 'pending')
                ->where('requires_approval', true)
                ->count(),
        ]);
    }
}
