<template>
  <div :class="{ 'sidebar-collapsed': collapsed, 'dark': darkMode }">
    <!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
    <nav class="sidebar d-flex flex-column">
      <!-- Brand -->
      <div class="sidebar-brand d-flex align-items-center gap-2">
        <img src="/images/logo.png" alt="NABAAD Bank"
             style="height:38px;width:auto;filter:brightness(0) invert(1);"
             @error="$event.target.style.display='none'">
        <div class="brand-text">
          <div class="brand-name">NABAAD Bank</div>
          <div style="color: rgba(255,255,255,0.5); font-size: 0.7rem;">Garowe Branch</div>
        </div>
        <button type="button" class="sidebar-toggle-btn ms-auto" @click="toggleSidebar"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
          <i class="bi" :class="collapsed ? 'bi-chevron-right' : 'bi-chevron-left'"></i>
        </button>
      </div>

      <!-- Navigation -->
      <ul class="nav flex-column pt-2 flex-grow-1" style="overflow-y: auto; overflow-x: hidden; min-height: 0; flex-wrap: nowrap;">
        <span class="sidebar-section-title">Main</span>

        <li class="nav-item">
          <Link :href="route('admin.dashboard')" class="nav-link" :class="{ active: isActive('admin.dashboard') }">
            <i class="bi bi-speedometer2"></i> <span class="nav-label">Dashboard</span>
          </Link>
        </li>

        <span class="sidebar-section-title mt-2">Banking</span>

        <li v-if="can('customers.view')" class="nav-item">
          <Link :href="route('admin.customers.index')" class="nav-link" :class="{ active: isActive('admin.customers') }">
            <i class="bi bi-people"></i> <span class="nav-label">Customers</span>
          </Link>
        </li>

        <li v-if="can('accounts.view')" class="nav-item">
          <Link
            :href="route('admin.accounts.index')"
            class="nav-link"
            :class="{ active: isActive('admin.accounts') }"
          >
            <i class="bi bi-wallet2"></i> <span class="nav-label">Accounts</span>
          </Link>
        </li>

        <li v-if="can('transactions.view')" class="nav-item">
          <Link :href="route('admin.transactions.index')" class="nav-link" :class="{ active: isActive('admin.transactions') }">
            <i class="bi bi-arrow-left-right"></i> <span class="nav-label">Transactions</span>
          </Link>
        </li>

        <li v-if="can('transactions.transfer')" class="nav-item">
          <Link :href="route('admin.standing-orders.index')" class="nav-link" :class="{ active: isActive('admin.standing-orders') }">
            <i class="bi bi-repeat"></i> <span class="nav-label">Standing Orders</span>
          </Link>
        </li>

        <li v-if="can('loans.view')" class="nav-item">
          <Link :href="route('admin.loans.index')" class="nav-link" :class="{ active: isActive('admin.loans') }">
            <i class="bi bi-cash-coin"></i> <span class="nav-label">Loans</span>
          </Link>
        </li>

        <li v-if="can('cheques.view')" class="nav-item">
          <Link :href="route('admin.cheques.index')" class="nav-link" :class="{ active: isActive('admin.cheques') }">
            <i class="bi bi-file-earmark-text"></i> <span class="nav-label">Cheques</span>
          </Link>
        </li>

        <span class="sidebar-section-title mt-2">Operations</span>

        <li class="nav-item">
          <Link :href="route('admin.business-day.index')" class="nav-link" :class="{ active: isActive('admin.business-day') }">
            <i class="bi bi-calendar-check"></i> <span class="nav-label">Business Day</span>
          </Link>
        </li>

        <li v-if="can('teller.open-till') || can('teller.close-till')" class="nav-item">
          <Link :href="route('admin.tellers.index')" class="nav-link" :class="{ active: isActive('admin.tellers') }">
            <i class="bi bi-cash-stack"></i> <span class="nav-label">Teller Operations</span>
          </Link>
        </li>

        <li v-if="can('vault.view')" class="nav-item">
          <Link :href="route('admin.vault.show')" class="nav-link" :class="{ active: isActive('admin.vault') }">
            <i class="bi bi-safe"></i> <span class="nav-label">Vault</span>
          </Link>
        </li>

        <li v-if="can('approvals.view')" class="nav-item">
          <Link :href="route('admin.approvals.index')" class="nav-link" :class="{ active: isActive('admin.approvals') }">
            <i class="bi bi-check2-circle"></i> <span class="nav-label">Approvals</span>
            <span v-if="$page.props.pending_approvals > 0"
              class="badge rounded-pill bg-danger ms-auto"
              style="font-size:.65rem">{{ $page.props.pending_approvals }}</span>
          </Link>
        </li>

        <li v-if="can('kyc.view')" class="nav-item">
          <Link :href="route('admin.kyc.index')" class="nav-link" :class="{ active: isActive('admin.kyc') }">
            <i class="bi bi-person-check"></i> <span class="nav-label">KYC</span>
          </Link>
        </li>

        <span class="sidebar-section-title mt-2">Management</span>

        <li v-if="can('reports.view')" class="nav-item">
          <Link :href="route('admin.reports.index')" class="nav-link" :class="{ active: isActive('admin.reports') }">
            <i class="bi bi-bar-chart-line"></i> <span class="nav-label">Reports</span>
          </Link>
        </li>

        <li v-if="can('audit.view')" class="nav-item">
          <Link :href="route('admin.audit-logs.index')" class="nav-link" :class="{ active: isActive('admin.audit-logs') }">
            <i class="bi bi-journal-text"></i> <span class="nav-label">Audit Logs</span>
          </Link>
        </li>

        <li v-if="can('users.view')" class="nav-item">
          <Link :href="route('admin.users.index')" class="nav-link" :class="{ active: isActive('admin.users') }">
            <i class="bi bi-person-gear"></i> <span class="nav-label">Staff Management</span>
          </Link>
        </li>

        <li v-if="can('roles.manage')" class="nav-item">
          <Link :href="route('admin.roles.index')" class="nav-link" :class="{ active: isActive('admin.roles') }">
            <i class="bi bi-shield-lock"></i> <span class="nav-label">Roles &amp; Permissions</span>
          </Link>
        </li>

        <li v-if="can('settings.view')" class="nav-item">
          <Link :href="route('admin.settings.index')" class="nav-link" :class="{ active: isActive('admin.settings') }">
            <i class="bi bi-gear"></i> <span class="nav-label">Settings</span>
          </Link>
        </li>

        <li v-if="can('eod.run')" class="nav-item">
          <Link :href="route('admin.eod.index')" class="nav-link" :class="{ active: isActive('admin.eod') }">
            <i class="bi bi-moon-stars"></i> <span class="nav-label">End of Day</span>
          </Link>
        </li>

        <li v-if="can('eod.run')" class="nav-item">
          <Link :href="route('admin.public-holidays.index')" class="nav-link" :class="{ active: isActive('admin.public-holidays') }">
            <i class="bi bi-calendar-event"></i> <span class="nav-label">Public Holidays</span>
          </Link>
        </li>
      </ul>

      <!-- User info at bottom — clicking goes to profile -->
      <Link :href="route('admin.profile')"
            class="p-3 border-top d-flex align-items-center gap-2 user-info-row text-decoration-none sidebar-profile-btn"
            style="border-color: rgba(255,255,255,0.1) !important;"
            :class="{ active: isActive('admin.profile') }">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center"
             style="width:34px;height:34px;flex-shrink:0;">
          <i class="bi bi-person-fill" style="color:#0A2E5D;"></i>
        </div>
        <div class="overflow-hidden user-info-text">
          <div class="text-white small fw-semibold text-truncate">{{ auth.name }}</div>
          <div style="color:rgba(255,255,255,0.5);font-size:0.7rem;" class="text-truncate">
            {{ auth.role }} &nbsp;·&nbsp; My Profile
          </div>
        </div>
      </Link>
    </nav>

    <!-- ── Top Bar ──────────────────────────────────────────────────────────── -->
    <header class="topbar justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
              <Link :href="route('admin.dashboard')" class="text-decoration-none" style="color:#0A2E5D;">
                <i class="bi bi-house-door"></i>
              </Link>
            </li>
            <li
              v-for="(crumb, i) in breadcrumbs"
              :key="i"
              class="breadcrumb-item"
              :class="{ active: i === breadcrumbs.length - 1 }"
            >
              <Link v-if="crumb.href" :href="crumb.href" class="text-decoration-none" style="color:#0A2E5D;">
                {{ crumb.label }}
              </Link>
              <span v-else>{{ crumb.label }}</span>
            </li>
          </ol>
        </nav>
      </div>

      <div class="d-flex align-items-center gap-3">
        <!-- Clock -->
        <span class="text-muted small d-none d-md-block">
          <i class="bi bi-clock me-1"></i>{{ currentTime }}
        </span>

        <!-- Dark / Light toggle -->
        <button type="button" class="btn btn-light btn-sm" @click="toggleDark" :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
          <i class="bi" :class="darkMode ? 'bi-sun-fill text-warning' : 'bi-moon-fill text-secondary'"></i>
        </button>

        <!-- Notifications bell (only for users who can approve) -->
        <div v-if="can('approvals.view')" class="dropdown">
          <button
            type="button"
            class="btn btn-light btn-sm position-relative"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Pending Approvals"
          >
            <i class="bi bi-bell"></i>
            <span v-if="$page.props.pending_approvals > 0"
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="font-size:0.6rem;">{{ $page.props.pending_approvals }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:260px;">
            <li>
              <h6 class="dropdown-header d-flex align-items-center justify-content-between">
                Pending Approvals
                <span v-if="$page.props.pending_approvals > 0"
                      class="badge bg-danger rounded-pill">{{ $page.props.pending_approvals }}</span>
                <span v-else class="badge bg-secondary rounded-pill">0</span>
              </h6>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li v-if="$page.props.pending_approvals === 0">
              <span class="dropdown-item-text text-muted small py-2 d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-success"></i>
                No transactions awaiting your approval.
              </span>
            </li>
            <li v-else>
              <span class="dropdown-item-text text-muted small py-2 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle text-warning"></i>
                {{ $page.props.pending_approvals }} transaction{{ $page.props.pending_approvals !== 1 ? 's' : '' }} need your review.
              </span>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
              <Link :href="route('admin.approvals.index')" class="dropdown-item small">
                <i class="bi bi-arrow-right-circle me-2 text-primary"></i>View Approvals Queue
              </Link>
            </li>
          </ul>
        </div>

        <!-- User dropdown -->
        <div class="dropdown">
          <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-md-inline">{{ auth.name }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header">{{ auth.role }}</h6></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <Link :href="route('admin.profile')" class="dropdown-item">
                <i class="bi bi-person me-2"></i>Profile
              </Link>
            </li>
            <li>
              <form @submit.prevent="logout">
                <button type="submit" class="dropdown-item text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- ── Page Content ─────────────────────────────────────────────────────── -->
    <main class="page-wrapper">
      <!-- Toast Notifications -->
      <div class="toast-container">
        <div
          v-if="$page.props.flash?.success"
          class="toast show align-items-center text-bg-success border-0"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi bi-check-circle me-2"></i>{{ $page.props.flash.success }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>
        <div
          v-if="$page.props.flash?.error"
          class="toast show align-items-center text-bg-danger border-0"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi bi-exclamation-circle me-2"></i>{{ $page.props.flash.error }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>
      </div>

      <div class="page-content">
        <!-- Page Header -->
        <div v-if="title" class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="fw-bold mb-0" style="color:#0A2E5D;">{{ title }}</h5>
            <p v-if="subtitle" class="text-muted small mb-0">{{ subtitle }}</p>
          </div>
          <slot name="actions" />
        </div>

        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  title: String,
  subtitle: String,
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();

// Sidebar collapse — persisted across visits
const collapsed = ref(localStorage.getItem('nabaad_sidebar_collapsed') === '1');
const toggleSidebar = () => {
  collapsed.value = !collapsed.value;
  localStorage.setItem('nabaad_sidebar_collapsed', collapsed.value ? '1' : '0');
};

// Dark / Light mode — persisted across visits
const darkMode = ref(localStorage.getItem('nabaad_dark_mode') === '1');
const toggleDark = () => {
  darkMode.value = !darkMode.value;
  localStorage.setItem('nabaad_dark_mode', darkMode.value ? '1' : '0');
};

const auth = computed(() => ({
  name: page.props.auth?.user?.name ?? 'Staff',
  role: page.props.auth?.user?.roles?.[0]?.name ?? '',
}));

// Permission helper — reads from Inertia shared props
const can = (permission) => {
  return page.props.auth?.permissions?.includes(permission) ?? false;
};

// Active route helper — supports exact name or prefix (e.g. 'admin.customers')
const isActive = (prefix) => {
  const current = route().current() ?? '';
  return current === prefix || current.startsWith(prefix + '.');
};

// Clock
const currentTime = ref('');
let clockInterval;

const updateClock = () => {
  currentTime.value = new Date().toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Africa/Mogadishu',
  });
};

onMounted(() => {
  updateClock();
  clockInterval = setInterval(updateClock, 1000);
});

onUnmounted(() => clearInterval(clockInterval));

const logout = () => {
  router.post(route('logout'));
};
</script>
