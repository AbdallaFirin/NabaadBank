<template>
  <PortalLayout title="My Accounts" subtitle="All your bank accounts">
    <div class="row g-4">
      <div v-for="acc in accounts" :key="acc.id" class="col-md-6 col-xl-4">
        <div class="account-card">
          <div class="account-card-top">
            <div>
              <div class="text-white-50 small text-uppercase letter-spacing-1">{{ ucfirst(acc.account_type) }}</div>
              <div class="account-number">{{ acc.account_number }}</div>
            </div>
            <span class="badge" :class="statusBadge(acc.status)">{{ acc.status }}</span>
          </div>
          <div class="account-balance">
            <div class="balance-label">Available Balance</div>
            <div class="balance-amount">${{ fmt(acc.balance) }}</div>
          </div>
          <div class="account-card-footer">
            <div class="text-white-50 small">Opened {{ fmtDate(acc.created_at) }}</div>
            <Link :href="route('customer.accounts.show', acc.id)"
                  class="btn btn-sm btn-light fw-semibold">
              View <i class="bi bi-arrow-right ms-1"></i>
            </Link>
          </div>
        </div>
      </div>

      <div v-if="!accounts.length" class="col-12">
        <div class="text-center text-muted py-5">
          <i class="bi bi-wallet2 fs-1 mb-2 d-block"></i>
          No accounts found. Contact the bank to open an account.
        </div>
      </div>
    </div>
  </PortalLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PortalLayout from '@/Layouts/PortalLayout.vue'

defineProps({ accounts: { type: Array, default: () => [] } })

const fmt     = (v) => Number(v ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) : ''
const ucfirst = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, ' ') : ''
const statusBadge = (s) => ({ active: 'bg-success', frozen: 'bg-info', closed: 'bg-secondary', dormant: 'bg-warning text-dark' }[s] ?? 'bg-secondary')
</script>

<style scoped>
.account-card { background: linear-gradient(135deg, #0A2E5D 0%, #1a4a8a 100%); border-radius: 16px; padding: 1.5rem; color: #fff; box-shadow: 0 4px 16px rgba(10,46,93,.25); }
.account-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
.account-number { font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; font-family: monospace; }
.balance-label { font-size: .75rem; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .5px; }
.balance-amount { font-size: 2rem; font-weight: 800; }
.account-balance { margin-bottom: 1.5rem; }
.account-card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,.15); padding-top: 1rem; }
</style>
