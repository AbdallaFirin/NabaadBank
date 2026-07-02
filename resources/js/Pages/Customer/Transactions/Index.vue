<template>
  <PortalLayout title="Transactions" subtitle="Your complete transaction history">

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form @submit.prevent="applyFilters" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-semibold small">Account</label>
            <select v-model="f.account_id" class="form-select form-select-sm">
              <option value="">All Accounts</option>
              <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                {{ acc.account_number }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold small">Type</label>
            <select v-model="f.type" class="form-select form-select-sm">
              <option value="">All Types</option>
              <option value="deposit">Deposit</option>
              <option value="withdrawal">Withdrawal</option>
              <option value="transfer">Transfer</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold small">From</label>
            <input v-model="f.date_from" type="date" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold small">To</label>
            <input v-model="f.date_to" type="date" class="form-control form-control-sm">
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
              <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearFilters">
              Clear
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div v-if="!transactions.data.length" class="text-center text-muted py-5">
          <i class="bi bi-arrow-left-right fs-1 mb-2 d-block"></i>
          No transactions found.
        </div>
        <div class="table-responsive" v-else>
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Account</th>
                <th>Type</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Balance After</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="txn in transactions.data" :key="txn.id">
                <td class="small text-muted">{{ fmtDate(txn.created_at) }}</td>
                <td class="font-monospace small">{{ txn.reference }}</td>
                <td class="font-monospace small">{{ txn.account?.account_number }}</td>
                <td>
                  <span class="badge" :class="typeBadge(txn.type)">{{ ucfirst(txn.type) }}</span>
                </td>
                <td class="text-end fw-semibold" :class="txn.type === 'deposit' ? 'text-success' : 'text-danger'">
                  {{ txn.type === 'deposit' ? '+' : '-' }}${{ fmt(txn.amount) }}
                </td>
                <td class="text-end small">${{ fmt(txn.balance_after) }}</td>
                <td>
                  <Link :href="route('customer.transactions.show', txn.id)"
                        class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:.75rem">
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div v-if="transactions.last_page > 1" class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="text-muted small">
          Showing {{ transactions.from }}–{{ transactions.to }} of {{ transactions.total }}
        </span>
        <div class="d-flex gap-2">
          <Link v-if="transactions.prev_page_url" :href="transactions.prev_page_url"
                class="btn btn-sm btn-outline-secondary">Prev</Link>
          <Link v-if="transactions.next_page_url" :href="transactions.next_page_url"
                class="btn btn-sm btn-outline-primary">Next</Link>
        </div>
      </div>
    </div>
  </PortalLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import PortalLayout from '@/Layouts/PortalLayout.vue'

const props = defineProps({
  transactions: Object,
  accounts: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
})

const f = ref({ account_id: props.filters.account_id ?? '', type: props.filters.type ?? '', date_from: props.filters.date_from ?? '', date_to: props.filters.date_to ?? '' })

const applyFilters = () => {
  router.get(route('customer.transactions.index'), Object.fromEntries(Object.entries(f.value).filter(([,v]) => v)), { preserveState: true })
}
const clearFilters = () => {
  f.value = { account_id: '', type: '', date_from: '', date_to: '' }
  router.get(route('customer.transactions.index'))
}

const fmt      = (v) => Number(v ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const fmtDate  = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) : ''
const ucfirst  = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
const typeBadge = (t) => ({ deposit: 'bg-success', withdrawal: 'bg-danger', transfer: 'bg-primary', reversal: 'bg-warning text-dark' }[t] ?? 'bg-secondary')
</script>
