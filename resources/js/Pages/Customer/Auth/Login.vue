<template>
  <div class="login-root">
    <div class="login-card">
      <!-- Logo -->
      <div class="text-center mb-4">
        <img src="/images/logo.png" alt="NABAAD Bank" style="height:80px;width:auto"
             @error="$event.target.style.display='none'">
        <h4 class="fw-bold mt-2" style="color:#0A2E5D">Customer Portal</h4>
        <p class="text-muted small">Sign in to access your accounts</p>
      </div>

      <div v-if="status" class="alert alert-success small">{{ status }}</div>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label fw-semibold small">Email Address</label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
            <input v-model="form.email" type="email" class="form-control"
                   :class="{ 'is-invalid': form.errors.email }"
                   placeholder="you@example.com" autofocus required>
            <div class="invalid-feedback">{{ form.errors.email }}</div>
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold small mb-0">Password</label>
            <Link :href="route('customer.password.request')"
                  class="small text-decoration-none" style="color:#0A2E5D">
              Forgot password?
            </Link>
          </div>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
            <input v-model="form.password" :type="showPwd ? 'text' : 'password'"
                   class="form-control" :class="{ 'is-invalid': form.errors.password }"
                   placeholder="••••••••" required>
            <button type="button" class="input-group-text bg-white border-start-0"
                    @click="showPwd = !showPwd" tabindex="-1">
              <i class="bi" :class="showPwd ? 'bi-eye-slash' : 'bi-eye'" style="color:#94a3b8"></i>
            </button>
            <div class="invalid-feedback">{{ form.errors.password }}</div>
          </div>
        </div>

        <div class="mb-4 form-check">
          <input v-model="form.remember" type="checkbox" class="form-check-input" id="remember">
          <label class="form-check-label small" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn w-100 fw-semibold py-2"
                :disabled="form.processing"
                style="background:#0A2E5D;color:#fff;border-radius:8px">
          <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
          Sign In
        </button>
      </form>

      <p class="text-center text-muted small mt-4 mb-0">
        Staff login?
        <a :href="route('login')" class="text-decoration-none fw-semibold" style="color:#0A2E5D">Click here</a>
      </p>
    </div>

    <!-- Footer -->
    <p class="text-center text-muted small mt-4">
      &copy; {{ new Date().getFullYear() }} NABAAD Bank &middot; Trust &bull; Security &bull; Progress
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({ status: String })
const showPwd = ref(false)

const form = useForm({ email: '', password: '', remember: false })
const submit = () => form.post(route('customer.login'))
</script>

<style scoped>
.login-root { min-height: 100vh; background: linear-gradient(135deg, #0A2E5D 0%, #1a4a8a 50%, #0A2E5D 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem; }
.login-card { background: #fff; border-radius: 16px; padding: 2.5rem 2rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
</style>
