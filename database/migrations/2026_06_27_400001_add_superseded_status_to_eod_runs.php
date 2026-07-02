<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE eod_runs MODIFY COLUMN status ENUM(
            'running','completed','failed','superseded'
        ) NOT NULL DEFAULT 'running'");
    }

    public function down(): void
    {
        DB::statement("UPDATE eod_runs SET status = 'completed' WHERE status = 'superseded'");

        DB::statement("ALTER TABLE eod_runs MODIFY COLUMN status ENUM(
            'running','completed','failed'
        ) NOT NULL DEFAULT 'running'");
    }
};
