<template>
  <PortalLayout title="Dashboard" :subtitle="`Welcome back, ${customer_auth.name}`">

    <!-- Low-balance warnings -->
    <div v-for="acc in low_balance_accounts" :key="acc.id"
         class="alert alert-warning d-flex align-items-center gap-3 mb-3 py-2">
      <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
      <div class="small">
        Account <strong class="font-monospace">{{ acc.account_number }}</strong> has a low balance of
        <strong>${{ fmt(acc.balance) }}</strong>.
        <Link :href="route('customer.accounts.show', acc.id)" class="alert-link ms-1">View account</Link>
      </div>
    </div>

    <!-- Next instalment due -->
    <div v-if="next_due" class="alert mb-3 py-2 d-flex align-items-center gap-3"
         :class="next_due.status === 'overdue' ? 'alert-danger' : 'alert-info'">
      <i class="bi flex-shrink-0" :class="next_due.status === 'overdue' ? 'bi-exclamation-circle-fill' : 'bi-calendar-check'"></i>
      <div class="small flex-grow-1">
        <span v-if="next_due.status === 'overdue'"><strong>Overdue instalment</strong> on loan</span>
        <span v-else><strong>Next loan instalment</strong> due</span>
        <strong class="ms-1">{{ fmtDate(next_due.due_date) }}</strong>
        — <strong>${{ fmt(next_due.emi_amount) }}</strong>
      </div>
      <Link :href="route('customer.loans.show', next_due.loan_id)" class="btn btn-sm btn-outline-dark py-0">Pay</Link>
    </div>

    <!-- Summary stat cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-wallet2"></i></div>
          <div class="stat-label">Total Balance</div>
          <div class="stat-value">${{ fmt(stats.total_balance) }}</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:#d1fae5;color:#065f46"><i class="bi bi-bank2"></i></div>
          <div class="stat-label">Accounts</div>
          <div class="stat-value">{{ stats.account_count }}</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-cash-coin"></i></div>
          <div class="stat-label">Active Loans</div>
          <div class="stat-value">{{ stats.active_loans }}</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-person-check"></i></div>
          <div class="stat-label">Customer ID</div>
          <div class="stat-value font-monospace" style="font-size:1rem">{{ customer_auth.customer_number }}</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Accounts summary -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-wallet2 me-2 text-primary"></i>My Accounts</span>
            <Link :href="route('customer.accounts.index')"
                  class="btn btn-sm btn-outline-primary">View All</Link>
          </div>
          <div class="card-body p-0">
            <div v-if="!accounts.length" class="text-center text-muted py-4 small">No active accounts</div>
            <div v-for="acc in accounts" :key="acc.id" class="account-row">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold font-monospace small">{{ acc.account_number }}</div>
                  <div class="text-muted" style="font-size:.75rem">{{ ucfirst(acc.account_type) }}</div>
                </div>
                <div class="text-end">
                  <div class="fw-bold" style="color:#0A2E5D">${{ fmt(acc.balance) }}</div>
                  <span class="badge" :class="acc.status === 'active' ? 'bg-success' : 'bg-secondary'"
                        style="font-size:.65rem">{{ acc.status }}</span>
                </div>
              </div>
              <Link :href="route('customer.accounts.show', acc.id)"
                    class="stretched-link"></Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent transactions -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Transactions</span>
            <Link :href="route('customer.transactions.index')"
                  class="btn btn-sm btn-outline-primary">View All</Link>
          </div>
          <div class="card-body p-0">
            <div v-if="!recent_transactions.length" class="text-center text-muted py-4 small">No transactions yet</div>
            <div v-for="txn in recent_transactions" :key="txn.id" class="txn-row">
              <div class="d-flex align-items-center gap-3">
                <div class="txn-icon" :class="txnColor(txn.type)">
                  <i class="bi" :class="txnIcon(txn.type)"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold small">{{ ucfirst(txn.type) }}</div>
                  <div class="text-muted" style="font-size:.72rem">
                    {{ txn.account?.account_number }} · {{ fmtDate(txn.created_at) }}
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold" :class="txn.type === 'deposit' ? 'text-success' : 'text-danger'">
                    {{ txn.type === 'deposit' ? '+' : '-' }}${{ fmt(txn.amount) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PortalLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import PortalLayout from '@/Layouts/PortalLayout.vue'

const page = usePage()
const props = defineProps({
  customer:             Object,
  accounts:             { type: Array, default: () => [] },
  recent_transactions:  { type: Array, default: () => [] },
  stats:                Object,
  next_due:             { type: Object, default: null },
  low_balance_accounts: { type: Array,  default: () => [] },
})

const customer_auth = page.props.customer_auth ?? {}
const fmt     = (v) => Number(v ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) : ''
const ucfirst = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, ' ') : ''
const txnIcon  = (t) => ({ deposit: 'bi-arrow-down-circle-fill', withdrawal: 'bi-arrow-up-circle-fill', transfer: 'bi-arrow-left-right', reversal: 'bi-arrow-counterclockwise' }[t] ?? 'bi-circle')
const txnColor = (t) => t === 'deposit' ? 'txn-icon-in' : t === 'withdrawal' ? 'txn-icon-out' : 'txn-icon-other'
</script>

<style scoped>
.stat-card { background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:.75rem; }
.stat-label { font-size:.75rem; color:#64748b; text-transform:uppercase; letter-spacing:.5px; }
.stat-value { font-size:1.3rem; font-weight:700; color:#0A2E5D; }

.account-row { padding:.875rem 1.25rem; border-bottom:1px solid #f0f4f8; position:relative; cursor:pointer; transition:background .15s; }
.account-row:hover { background:#f8fafc; }
.account-row:last-child { border-bottom:none; }

.txn-row { padding:.75rem 1.25rem; border-bottom:1px solid #f0f4f8; }
.txn-row:last-child { border-bottom:none; }
.txn-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
.txn-icon-in   { background:#d1fae5; color:#065f46; }
.txn-icon-out  { background:#fee2e2; color:#991b1b; }
.txn-icon-other{ background:#dbeafe; color:#1d4ed8; }
</style>
