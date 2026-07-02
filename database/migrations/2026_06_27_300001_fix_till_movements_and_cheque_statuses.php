<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add customer_deposit / customer_withdrawal to till movement types ─
        DB::statement("ALTER TABLE till_cash_movements MODIFY COLUMN type ENUM(
            'replenishment','return','transfer_in','transfer_out',
            'customer_deposit','customer_withdrawal'
        ) NOT NULL");

        // ── 2. Migrate existing bad cheque statuses before altering enum ─────────
        DB::statement("UPDATE cheques SET status = 'used'             WHERE status = 'paid'");
        DB::statement("UPDATE cheques SET status = 'pending_clearance' WHERE status = 'deposited'");

        // ── 3. Remove 'paid' and 'deposited' from cheques status enum ─────────────
        DB::statement("ALTER TABLE cheques MODIFY COLUMN status ENUM(
            'issued','pending_clearance','cleared','used',
            'bounced','cancelled','expired'
        ) NOT NULL DEFAULT 'issued'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE till_cash_movements MODIFY COLUMN type ENUM(
            'replenishment','return','transfer_in','transfer_out'
        ) NOT NULL");

        DB::statement("ALTER TABLE cheques MODIFY COLUMN status ENUM(
            'issued','pending_clearance','cleared','used',
            'bounced','cancelled','expired','paid','deposited'
        ) NOT NULL DEFAULT 'issued'");
    }
};
