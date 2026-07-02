<template>
  <AdminLayout title="Dashboard" subtitle="Welcome back to NABAAD Bank">

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
              <div class="text-muted small">Active Customers</div>
              <div class="fw-bold fs-4">{{ stats.customers.toLocaleString() }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color:#10b981">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981">
              <i class="bi bi-wallet2"></i>
            </div>
            <div>
              <div class="text-muted small">Active Accounts</div>
              <div class="fw-bold fs-4">{{ stats.active_accounts.toLocaleString() }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color:#f59e0b">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b">
              <i class="bi bi-arrow-left-right"></i>
            </div>
            <div>
              <div class="text-muted small">Today's Transactions</div>
              <div class="fw-bold fs-4">{{ stats.today_transactions.toLocaleString() }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color:#6366f1">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1">
              <i class="bi bi-cash-coin"></i>
            </div>
            <div>
              <div class="text-muted small">Active Loans</div>
              <div class="fw-bold fs-4">{{ stats.active_loans.toLocaleString() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Approvals & Quick Actions -->
    <div class="row g-3">
      <!-- Pending Approvals -->
      <div class="col-lg-8">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-check2-circle me-2 text-warning"></i>Pending Approvals</span>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-warning text-dark">{{ pending_approvals_count }}</span>
              <Link :href="route('admin.approvals.index')" class="btn btn-xs btn-outline-secondary" style="font-size:.75rem;padding:.15rem .5rem">
                View all
              </Link>
            </div>
          </div>
          <div v-if="pending_approvals.length === 0" class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            No pending approvals
          </div>
          <div v-else class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Reference</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Account</th>
                  <th>Initiated By</th>
                  <th>Age</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="txn in pending_approvals" :key="txn.id">
                  <td class="font-monospace small">{{ txn.reference }}</td>
                  <td>
                    <span class="badge" :class="typeBadge(txn.type)">{{ txn.type.replace(/_/g,' ') }}</span>
                  </td>
                  <td class="font-monospace small">{{ fmt(txn.amount) }}</td>
                  <td class="small text-muted">{{ txn.account?.account_number ?? '—' }}</td>
                  <td class="small">{{ txn.processed_by?.name ?? '—' }}</td>
                  <td class="small text-muted">{{ age(txn.created_at) }}</td>
                  <td>
                    <Link :href="route('admin.approvals.show', txn.id)"
                          class="btn btn-xs btn-warning py-0 px-2" style="font-size:.72rem">
                      Review
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header">
            <i class="bi bi-lightning me-2 text-primary"></i>Quick Actions
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <Link :href="route('admin.customers.create')" class="btn btn-outline-primary btn-sm text-start">
                <i class="bi bi-person-plus me-2"></i>New Customer
              </Link>
              <Link :href="route('admin.accounts.create')" class="btn btn-outline-primary btn-sm text-start">
                <i class="bi bi-wallet-plus me-2"></i>Open Account
              </Link>
              <Link :href="route('admin.transactions.deposit.form')" class="btn btn-outline-success btn-sm text-start">
                <i class="bi bi-arrow-down-circle me-2"></i>Deposit Cash
              </Link>
              <Link :href="route('admin.transactions.withdrawal.form')" class="btn btn-outline-danger btn-sm text-start">
                <i class="bi bi-arrow-up-circle me-2"></i>Cash Withdrawal
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({ customers: 0, active_accounts: 0, today_transactions: 0, active_loans: 0 }),
  },
  pending_approvals:       { type: Array,  default: () => [] },
  pending_approvals_count: { type: Number, default: 0 },
})

const fmt = (v) =>
  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 2 }).format(v ?? 0)

const age = (d) => {
  const mins = Math.floor((Date.now() - new Date(d)) / 60000)
  if (mins < 60)   return `${mins}m ago`
  if (mins < 1440) return `${Math.floor(mins / 60)}h ago`
  return `${Math.floor(mins / 1440)}d ago`
}

const typeBadge = (t) => ({
  deposit:        'bg-success',
  withdrawal:     'bg-danger',
  transfer:       'bg-primary',
  loan_repayment: 'bg-info text-dark',
}[t] ?? 'bg-secondary')
</script>
