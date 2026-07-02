<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\EodRun;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EodService
{
    public function __construct(
        private StandingOrderService  $standingOrders,
        private ChequeService         $cheques,
        private LoanService           $loans,
    ) {}

    public function run(Carbon $date, bool $isManual = false): EodRun
    {
        // Prevent duplicate run for the same date
        $existing = EodRun::where('run_date', $date->toDateString())
            ->where('status', 'completed')
            ->first();

        if ($existing) {
            return $existing;
        }

        $run = EodRun::create([
            'run_date'     => $date->toDateString(),
            'status'       => 'running',
            'started_at'   => now(),
            'triggered_by' => Auth::id(),
            'is_manual'    => $isManual,
        ]);

        $errors = [];

        // ── 1. Process Standing Orders ────────────────────────────────────────
        try {
            $soResults = $this->standingOrders->executeDue();
            $run->standing_orders_success = $soResults['success'];
            $run->standing_orders_failed  = $soResults['failed'];
            $run->standing_orders_skipped = $soResults['skipped'] ?? 0;
        } catch (\Throwable $e) {
            $errors[] = ['step' => 'standing_orders', 'message' => $e->getMessage()];
            Log::error('[EOD] Standing orders failed', ['error' => $e->getMessage()]);
        }

        // ── 2. Clear Due Cheques ──────────────────────────────────────────────
        try {
            $run->cheques_cleared = $this->cheques->processClearing();
        } catch (\Throwable $e) {
            $errors[] = ['step' => 'cheques', 'message' => $e->getMessage()];
            Log::error('[EOD] Cheque clearing failed', ['error' => $e->getMessage()]);
        }

        // ── 3. Mark Overdue Loans / Apply Penalties ───────────────────────────
        try {
            $run->loans_marked_overdue = $this->loans->applyPenalties();
        } catch (\Throwable $e) {
            $errors[] = ['step' => 'loans', 'message' => $e->getMessage()];
            Log::error('[EOD] Loan overdue marking failed', ['error' => $e->getMessage()]);
        }

        // ── 4. Mark Dormant Accounts ──────────────────────────────────────────
        try {
            $run->dormant_accounts = $this->markDormantAccounts();
        } catch (\Throwable $e) {
            $errors[] = ['step' => 'dormancy', 'message' => $e->getMessage()];
            Log::error('[EOD] Dormancy marking failed', ['error' => $e->getMessage()]);
        }

        // ── 5. Process FD Maturities ──────────────────────────────────────────
        try {
            $run->matured_fds = $this->processFdMaturity($date);
        } catch (\Throwable $e) {
            $errors[] = ['step' => 'fd_maturity', 'message' => $e->getMessage()];
            Log::error('[EOD] FD maturity processing failed', ['error' => $e->getMessage()]);
        }

        // ── Finalise ──────────────────────────────────────────────────────────
        $run->status       = empty($errors) ? 'completed' : 'failed';
        $run->completed_at = now();
        $run->errors       = empty($errors) ? null : $errors;
        $run->save();

        return $run;
    }

    // ── Dormancy ──────────────────────────────────────────────────────────────
    // Mark active accounts as dormant if they have had no activity for N months.

    private function markDormantAccounts(): int
    {
        $months   = (int) DB::table('settings')->where('key', 'dormancy_months')->value('value') ?? 6;
        $cutoff   = now()->subMonths($months)->toDateString();
        $count    = 0;

        Account::where('status', 'active')
            ->where(function ($q) use ($cutoff) {
                // No activity recorded since cutoff, or account opened before cutoff with no activity date
                $q->where('last_activity_date', '<=', $cutoff)
                  ->orWhere(function ($q2) use ($cutoff) {
                      $q2->whereNull('last_activity_date')
                         ->where('opening_date', '<=', $cutoff);
                  });
            })
            ->chunkById(200, function ($accounts) use (&$count) {
                foreach ($accounts as $account) {
                    DB::transaction(function () use ($account) {
                        $account->update([
                            'status'        => 'dormant',
                            'dormant_since' => today(),
                        ]);
                        AuditLog::record('account_dormant', 'accounts',
                            "Account {$account->account_number} marked dormant (no activity for threshold period)");
                    });
                    $count++;
                }
            });

        return $count;
    }

    // ── FD Maturity ───────────────────────────────────────────────────────────
    // Mark fixed deposit accounts whose maturity date has been reached.

    private function processFdMaturity(Carbon $date): int
    {
        $count = 0;

        Account::where('account_type', 'fixed_deposit')
            ->where('status', 'active')
            ->whereNotNull('fd_maturity_date')
            ->where('fd_maturity_date', '<=', $date->toDateString())
            ->chunkById(100, function ($accounts) use (&$count) {
                foreach ($accounts as $account) {
                    DB::transaction(function () use ($account) {
                        $account->update(['status' => 'matured']);
                        AuditLog::record('fd_matured', 'accounts',
                            "Fixed deposit {$account->account_number} matured on {$account->fd_maturity_date}");
                    });
                    $count++;
                }
            });

        return $count;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function latestRun(): ?EodRun
    {
        return EodRun::latest('run_date')->first();
    }

    public function ranToday(): bool
    {
        return EodRun::where('run_date', today())
            ->where('status', 'completed')
            ->exists();
    }
}
