<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Customer Management
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            // KYC
            'kyc.view', 'kyc.approve', 'kyc.reject',
            // Account Management
            'accounts.view', 'accounts.create', 'accounts.freeze', 'accounts.close', 'accounts.reactivate',
            // Transactions
            'transactions.view', 'transactions.deposit', 'transactions.withdraw', 'transactions.transfer',
            'transactions.reverse',
            // Approvals
            'approvals.view', 'approvals.approve', 'approvals.reject',
            // Teller Operations
            'teller.open-till', 'teller.close-till', 'teller.assign', 'teller.transfer', 'teller.reconcile',
            // Vault
            'vault.view', 'vault.cash-in', 'vault.cash-out', 'vault.transfer',
            // Loans
            'loans.view', 'loans.create', 'loans.review', 'loans.approve', 'loans.disburse', 'loans.close',
            // Cheques
            'cheques.view', 'cheques.issue', 'cheques.verify', 'cheques.cash', 'cheques.cancel',
            // Reports
            'reports.view', 'reports.export',
            // Audit Logs
            'audit.view',
            // System Settings
            'settings.view', 'settings.edit',
            // End of Day
            'eod.run', 'eod.reopen',
            // Business Day
            'business-day.manage',
            // Public Holidays
            'public-holidays.manage',
            // Roles & Permissions
            'roles.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Roles ─────────────────────────────────────────────────────────────

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::where('guard_name', 'web')->get());

        $branchManager = Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']);
        $branchManager->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'kyc.view', 'kyc.approve', 'kyc.reject',
            'accounts.view', 'accounts.create', 'accounts.freeze', 'accounts.close', 'accounts.reactivate',
            'transactions.view', 'transactions.reverse',
            'approvals.view', 'approvals.approve', 'approvals.reject',
            'teller.close-till', 'teller.assign', 'teller.reconcile',
            'vault.view', 'vault.cash-in', 'vault.cash-out', 'vault.transfer',
            'loans.view', 'loans.approve',
            'cheques.view', 'cheques.verify',
            'reports.view', 'reports.export',
            'audit.view',
            'settings.view',
            'eod.run', 'eod.reopen',
            'business-day.manage',
            'public-holidays.manage',
        ]);

        $compliance = Role::firstOrCreate(['name' => 'Compliance Officer', 'guard_name' => 'web']);
        $compliance->syncPermissions([
            'customers.view', 'kyc.view', 'kyc.approve', 'kyc.reject',
            'transactions.view', 'transactions.reverse',
            'approvals.view', 'approvals.approve', 'approvals.reject',
            'loans.view', 'loans.approve',
            'reports.view', 'reports.export',
            'audit.view',
        ]);

        $tellerSupervisor = Role::firstOrCreate(['name' => 'Teller Supervisor', 'guard_name' => 'web']);
        $tellerSupervisor->syncPermissions([
            'customers.view', 'accounts.view',
            'transactions.view', 'transactions.deposit', 'transactions.withdraw', 'transactions.transfer',
            'transactions.reverse',
            'teller.open-till', 'teller.close-till', 'teller.assign', 'teller.transfer',
            'teller.reconcile',
            'vault.view',
            'cheques.view', 'cheques.verify', 'cheques.cash',
            'reports.view',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'customers.view', 'accounts.view',
            'transactions.view', 'transactions.deposit', 'transactions.withdraw', 'transactions.transfer',
            'approvals.view',
            'vault.view', 'vault.cash-in', 'vault.cash-out',
            'loans.view', 'loans.disburse', 'loans.close',
            'reports.view', 'reports.export',
            'audit.view',
        ]);

        $loanOfficer = Role::firstOrCreate(['name' => 'Loan Officer', 'guard_name' => 'web']);
        $loanOfficer->syncPermissions([
            'customers.view', 'accounts.view',
            'loans.view', 'loans.create', 'loans.review',
            'reports.view',
        ]);

        $teller = Role::firstOrCreate(['name' => 'Teller', 'guard_name' => 'web']);
        $teller->syncPermissions([
            'customers.view', 'accounts.view',
            'transactions.view', 'transactions.deposit', 'transactions.withdraw', 'transactions.transfer',
            'teller.open-till', 'teller.close-till',
            'cheques.view', 'cheques.cash',
        ]);

        $cso = Role::firstOrCreate(['name' => 'Customer Service Officer', 'guard_name' => 'web']);
        $cso->syncPermissions([
            'customers.view', 'customers.create', 'customers.edit',
            'kyc.view',
            'accounts.view', 'accounts.create',
            'transactions.view',
            'cheques.view', 'cheques.issue',
            'reports.view',
        ]);

        $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);
        $auditor->syncPermissions([
            'transactions.view', 'accounts.view', 'customers.view',
            'loans.view', 'cheques.view', 'vault.view',
            'reports.view', 'reports.export',
            'audit.view',
        ]);
    }
}
