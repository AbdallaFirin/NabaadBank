<template>
  <div class="portal-root">

    <!-- ── Top Nav ──────────────────────────────────────────────────────────── -->
    <nav class="portal-nav">
      <div class="portal-nav-inner">
        <!-- Logo -->
        <Link :href="route('customer.dashboard')" class="portal-brand">
          <img src="/images/logo.png" alt="NABAAD Bank" class="portal-logo"
               @error="$event.target.style.display='none'">
          <span class="portal-brand-text">NABAAD Bank</span>
        </Link>

        <!-- Desktop nav links -->
        <div class="portal-links d-none d-md-flex">
          <Link :href="route('customer.dashboard')"
                class="portal-link" :class="{ active: isActive('customer.dashboard') }">
            <i class="bi bi-speedometer2"></i> Dashboard
          </Link>
          <Link :href="route('customer.accounts.index')"
                class="portal-link" :class="{ active: isActive('customer.accounts') }">
            <i class="bi bi-wallet2"></i> Accounts
          </Link>
          <Link :href="route('customer.transactions.index')"
                class="portal-link" :class="{ active: isActive('customer.transactions') }">
            <i class="bi bi-arrow-left-right"></i> Transactions
          </Link>
          <Link :href="route('customer.loans.index')"
                class="portal-link" :class="{ active: isActive('customer.loans') }">
            <i class="bi bi-cash-coin"></i> Loans
          </Link>
          <Link :href="route('customer.cheques.index')"
                class="portal-link" :class="{ active: isActive('customer.cheques') }">
            <i class="bi bi-file-earmark-text"></i> Cheques
          </Link>
          <Link :href="route('customer.standing-orders.index')"
                class="portal-link" :class="{ active: isActive('customer.standing-orders') }">
            <i class="bi bi-arrow-repeat"></i> Orders
          </Link>
        </div>

        <!-- Right: user + logout -->
        <div class="d-flex align-items-center gap-3">
          <div class="d-none d-md-block text-end">
            <div class="fw-semibold small" style="color:#0A2E5D">{{ customer.name }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ customer.customer_number }}</div>
          </div>
          <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">
              <i class="bi bi-person-circle fs-5" style="color:#0A2E5D"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
              <li><h6 class="dropdown-header">{{ customer.name }}</h6></li>
              <li><span class="dropdown-item-text small text-muted">{{ customer.email }}</span></li>
              <li><hr class="dropdown-divider"></li>
              <li v-if="customer.last_login_at">
                <span class="dropdown-item-text small text-muted">
                  <i class="bi bi-clock me-1"></i>Last login: {{ customer.last_login_at }}
                </span>
              </li>
              <li>
                <Link :href="route('customer.profile.show')" class="dropdown-item small">
                  <i class="bi bi-person-gear me-2"></i>My Profile
                </Link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form @submit.prevent="logout">
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                  </button>
                </form>
              </li>
            </ul>
          </div>

          <!-- Mobile hamburger -->
          <button class="btn btn-light btn-sm d-md-none" @click="mobileOpen = !mobileOpen">
            <i class="bi" :class="mobileOpen ? 'bi-x-lg' : 'bi-list'"></i>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="mobileOpen" class="portal-mobile-menu">
        <Link :href="route('customer.dashboard')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </Link>
        <Link :href="route('customer.accounts.index')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-wallet2 me-2"></i>Accounts
        </Link>
        <Link :href="route('customer.transactions.index')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-arrow-left-right me-2"></i>Transactions
        </Link>
        <Link :href="route('customer.loans.index')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-cash-coin me-2"></i>Loans
        </Link>
        <Link :href="route('customer.cheques.index')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-file-earmark-text me-2"></i>Cheques
        </Link>
        <Link :href="route('customer.standing-orders.index')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-arrow-repeat me-2"></i>Standing Orders
        </Link>
        <Link :href="route('customer.profile.show')"
              class="portal-mobile-link" @click="mobileOpen=false">
          <i class="bi bi-person-gear me-2"></i>My Profile
        </Link>
      </div>
    </nav>

    <!-- ── Page Body ─────────────────────────────────────────────────────────── -->
    <main class="portal-main">

      <!-- Flash -->
      <div v-if="$page.props.flash?.success"
           class="alert alert-success alert-dismissible d-flex align-items-center mb-0 rounded-0 border-0"
           style="border-left:4px solid #10b981!important">
        <i class="bi bi-check-circle me-2"></i>{{ $page.props.flash.success }}
        <button type="button" class="btn-close ms-auto" @click="clearFlash"></button>
      </div>
      <div v-if="$page.props.flash?.error"
           class="alert alert-danger alert-dismissible d-flex align-items-center mb-0 rounded-0 border-0"
           style="border-left:4px solid #ef4444!important">
        <i class="bi bi-exclamation-circle me-2"></i>{{ $page.props.flash.error }}
        <button type="button" class="btn-close ms-auto" @click="clearFlash"></button>
      </div>

      <div class="portal-content">
        <!-- Page header -->
        <div v-if="title" class="portal-page-header">
          <div>
            <h5 class="fw-bold mb-0 portal-title">{{ title }}</h5>
            <p v-if="subtitle" class="text-muted small mb-0">{{ subtitle }}</p>
          </div>
          <slot name="actions" />
        </div>

        <slot />
      </div>
    </main>

    <!-- ── Footer ────────────────────────────────────────────────────────────── -->
    <footer class="portal-footer">
      <span>&copy; {{ new Date().getFullYear() }} NABAAD Bank &middot; Trust &bull; Security &bull; Progress</span>
      <span class="d-none d-md-inline text-muted small">Garowe Branch, Somalia</span>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

defineProps({ title: String, subtitle: String })

const page     = usePage()
const customer = computed(() => page.props.customer_auth ?? {})
const mobileOpen = ref(false)

const isActive = (prefix) => {
  const current = route().current() ?? ''
  return current === prefix || current.startsWith(prefix + '.')
}

const logout = () => router.post(route('customer.logout'))
const clearFlash = () => {}
</script>

<style scoped>
/* Portal root layout */
.portal-root { min-height: 100vh; display: flex; flex-direction: column; background: #f0f4f8; }

/* Nav */
.portal-nav { background: #fff; border-bottom: 2px solid #0A2E5D; box-shadow: 0 2px 8px rgba(0,0,0,.06); position: sticky; top: 0; z-index: 1000; }
.portal-nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; height: 64px; display: flex; align-items: center; gap: 2rem; }

/* Brand */
.portal-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.portal-logo { height: 40px; width: auto; }
.portal-brand-text { font-size: 1.1rem; font-weight: 700; color: #0A2E5D; white-space: nowrap; }

/* Nav links */
.portal-links { display: flex; gap: 4px; flex: 1; }
.portal-link { display: flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 6px; text-decoration: none; color: #64748b; font-size: .9rem; font-weight: 500; transition: all .15s; }
.portal-link:hover { background: #f0f4f8; color: #0A2E5D; }
.portal-link.active { background: #0A2E5D; color: #fff; }
.portal-link.active:hover { background: #0d3a7a; color: #fff; }

/* Mobile menu */
.portal-mobile-menu { border-top: 1px solid #e2e8f0; padding: .5rem; display: flex; flex-direction: column; gap: 2px; }
.portal-mobile-link { display: flex; align-items: center; padding: 10px 16px; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 500; }
.portal-mobile-link:hover { background: #f0f4f8; }

/* Main */
.portal-main { flex: 1; }
.portal-content { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }
.portal-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; }
.portal-title { color: #0A2E5D; font-size: 1.25rem; }

/* Footer */
.portal-footer { background: #0A2E5D; color: rgba(255,255,255,.6); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; font-size: .8rem; }
</style>
