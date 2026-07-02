<template>
  <PortalLayout :title="transaction.reference" subtitle="Transaction Details">
    <template #actions>
      <div class="d-flex gap-2">
        <a :href="route('admin.reports.receipt', transaction.id)" target="_blank"
           class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-printer me-1"></i>Receipt
        </a>
        <Link :href="route('customer.transactions.index')" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Back
        </Link>
      </div>
    </template>

    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <!-- Amount block -->
          <div class="text-center py-4 px-3" style="background:linear-gradient(135deg,#0A2E5D,#1a4a8a);border-radius:12px 12px 0 0">
            <div class="text-white-50 small text-uppercase letter-spacing-1">{{ ucfirst(transaction.type) }}</div>
            <div class="display-5 fw-bold text-white my-2">${{ fmt(transaction.amount) }}</div>
            <span class="badge bg-success px-3 py-1">{{ transaction.status?.toUpperCase() }}</span>
          </div>
          <div class="card-body">
            <div class="detail-row"><span>Reference</span><strong class="font-monospace small">{{ transaction.reference }}</strong></div>
            <div class="detail-row"><span>Account</span><strong class="font-monospace">{{ transaction.account?.account_number }}</strong></div>
            <div v-if="transaction.related_account" class="detail-row">
              <span>To Account</span><strong class="font-monospace">{{ transaction.related_account?.account_number }}</strong>
            </div>
            <div class="detail-row"><span>Balance Before</span><strong>${{ fmt(transaction.balance_before) }}</strong></div>
            <div class="detail-row"><span>Balance After</span><strong style="color:#0A2E5D">${{ fmt(transaction.balance_after) }}</strong></div>
            <div v-if="transaction.description" class="detail-row"><span>Description</span><strong>{{ transaction.description }}</strong></div>
            <div class="detail-row"><span>Date & Time</span><strong>{{ fmtDatetime(transaction.created_at) }}</strong></div>
            <div class="detail-row"><span>Processed By</span><strong>{{ transaction.processed_by?.name ?? 'System' }}</strong></div>
          </div>
          <div class="card-footer text-center text-muted small bg-white">
            Thank you for banking with NABAAD Bank
          </div>
        </div>
      </div>
    </div>
  </PortalLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PortalLayout from '@/Layouts/PortalLayout.vue'

defineProps({ transaction: Object })

const fmt         = (v) => Number(v ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const fmtDatetime = (d) => d ? new Date(d).toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }) : ''
const ucfirst     = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
</script>

<style scoped>
.detail-row { display: flex; justify-content: space-between; align-items: center; padding: .5rem 0; border-bottom: 1px solid #f0f4f8; font-size: .875rem; gap: 1rem; }
.detail-row:last-child { border-bottom: none; }
.detail-row span { color: #64748b; flex-shrink: 0; }
.detail-row strong { text-align: right; }
</style>
