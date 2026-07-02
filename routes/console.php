<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── EOD Master Process (runs all sub-tasks in order) ─────────────────────────
// Runs at midnight; individual commands kept available for manual use
Schedule::command('eod:run')->dailyAt('00:01')->withoutOverlapping();

// ── Approval Escalation ───────────────────────────────────────────────────────
// Flags transactions pending approval for over 24 hours so they surface to Compliance/Super Admin
Schedule::command('approvals:escalate-stale')->hourly()->withoutOverlapping();

// Individual commands still available for manual runs / debugging
// Schedule::command('standing-orders:execute')->dailyAt('08:00');
// Schedule::command('loans:mark-overdue')->dailyAt('07:00');
// Schedule::command('cheques:process-clearing')->dailyAt('09:00');
