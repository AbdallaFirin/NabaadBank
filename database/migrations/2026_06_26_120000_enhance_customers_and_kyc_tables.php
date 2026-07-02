<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add new columns to customers ──────────────────────────────────────
        Schema::table('customers', function (Blueprint $table) {
            $table->string('national_id', 50)->nullable()->unique()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('nationality', 100)->nullable()->after('gender');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('nationality');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('next_of_kin_name')->nullable()->after('city');
            $table->string('next_of_kin_phone', 20)->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_relationship', 100)->nullable()->after('next_of_kin_phone');
        });

        // ── Add 'pending' to customers.status enum and change default ─────────
        DB::statement("ALTER TABLE customers MODIFY COLUMN status ENUM('pending','active','inactive','blacklisted','deceased') NOT NULL DEFAULT 'pending'");

        // ── Add 'state_id' to kyc_documents.document_type enum ───────────────
        DB::statement("ALTER TABLE kyc_documents MODIFY COLUMN document_type ENUM('national_id','passport','driving_license','state_id') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'national_id', 'gender', 'nationality', 'marital_status',
                'city', 'next_of_kin_name', 'next_of_kin_phone', 'next_of_kin_relationship',
            ]);
        });

        DB::statement("ALTER TABLE customers MODIFY COLUMN status ENUM('active','inactive','blacklisted','deceased') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE kyc_documents MODIFY COLUMN document_type ENUM('national_id','passport','driving_license') NOT NULL");
    }
};
