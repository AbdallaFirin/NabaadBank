<template>
  <div class="login-page">
    <div class="login-card card p-4 p-md-5">
      <!-- Logo & Title -->
      <div class="text-center mb-4">
        <div class="mb-3">
          <i class="bi bi-bank2 text-primary" style="font-size: 2.5rem; color: #0A2E5D !important;"></i>
        </div>
        <h4 class="fw-bold mb-0" style="color: #0A2E5D;">NABAAD Bank</h4>
        <p class="text-muted small mb-0">Staff Portal — Garowe Branch</p>
      </div>

      <!-- Session expired warning -->
      <div v-if="$page.props.flash?.warning" class="alert alert-warning alert-sm py-2 small">
        <i class="bi bi-exclamation-triangle me-1"></i>
        {{ $page.props.flash.warning }}
      </div>

      <!-- Status message (password reset etc.) -->
      <div v-if="status" class="alert alert-success alert-sm py-2 small">{{ status }}</div>

      <form @submit.prevent="submit">
        <!-- Staff ID -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Staff ID</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-person-badge text-muted"></i>
            </span>
            <input
              v-model="form.staff_id"
              type="text"
              class="form-control border-start-0 ps-0 text-uppercase"
              :class="{ 'is-invalid': form.errors.staff_id }"
              placeholder="STF-0001"
              autocomplete="username"
              required
            />
            <div v-if="form.errors.staff_id" class="invalid-feedback">{{ form.errors.staff_id }}</div>
          </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <label class="form-label fw-semibold small">Password</label>
            <Link :href="route('password.request')" class="small text-decoration-none" style="color: #0A2E5D;">
              Forgot password?
            </Link>
          </div>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-lock text-muted"></i>
            </span>
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-control border-start-0 border-end-0 ps-0"
              :class="{ 'is-invalid': form.errors.password }"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
            <button
              type="button"
              class="input-group-text bg-light"
              @click="showPassword = !showPassword"
            >
              <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'" class="text-muted"></i>
            </button>
            <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
          </div>
        </div>

        <!-- Remember me -->
        <div class="mb-4 form-check">
          <input v-model="form.remember" type="checkbox" class="form-check-input" id="remember" />
          <label class="form-check-label small" for="remember">Remember me on this device</label>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="btn btn-primary w-100 fw-semibold"
          :disabled="form.processing"
        >
          <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
          <i v-else class="bi bi-box-arrow-in-right me-2"></i>
          Sign In to Staff Portal
        </button>
      </form>

      <p class="text-center text-muted small mt-4 mb-0">
        <i class="bi bi-shield-lock me-1"></i>
        Secured • NABAAD Bank © {{ new Date().getFullYear() }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  canResetPassword: Boolean,
  status: String,
});

const showPassword = ref(false);

const form = useForm({
  staff_id: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>
