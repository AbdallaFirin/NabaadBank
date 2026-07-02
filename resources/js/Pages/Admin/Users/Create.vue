<template>
  <AdminLayout
    title="Add Staff"
    subtitle="Create a new staff account"
    :breadcrumbs="[{ label: 'Staff Management', href: route('admin.users.index') }, { label: 'Add Staff' }]"
  >
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <form @submit.prevent="submit">
              <div class="alert alert-light border small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                A Staff ID (e.g. <span class="font-monospace">STF-0011</span>) will be generated automatically — staff use it to log in instead of their email.
              </div>

              <!-- Name -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="Enter full name"
                  autocomplete="name"
                />
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  placeholder="staff@nabaadbank.so"
                  autocomplete="email"
                />
                <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
              </div>

              <!-- Phone -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Phone Number</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="form-control"
                  :class="{ 'is-invalid': errors.phone }"
                  placeholder="+252 61 XXXXXXX"
                />
                <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
              </div>

              <!-- Role -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                <select v-model="form.role" class="form-select" :class="{ 'is-invalid': errors.role }">
                  <option value="">— Select Role —</option>
                  <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>
                <div v-if="errors.role" class="invalid-feedback">{{ errors.role }}</div>
              </div>

              <!-- Transaction Limit -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Daily Transaction Limit (USD)</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input
                    v-model="form.transaction_limit"
                    type="number"
                    min="0"
                    step="0.01"
                    class="form-control"
                    :class="{ 'is-invalid': errors.transaction_limit }"
                    placeholder="0.00"
                  />
                  <div v-if="errors.transaction_limit" class="invalid-feedback">{{ errors.transaction_limit }}</div>
                </div>
              </div>

              <!-- Status -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select v-model="form.status" class="form-select" :class="{ 'is-invalid': errors.status }">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>
                <div v-if="errors.status" class="invalid-feedback">{{ errors.status }}</div>
              </div>

              <hr class="my-4" />
              <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.5px">
                <i class="bi bi-lock me-1"></i> Set Password
              </h6>

              <!-- Password -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input
                    v-model="form.password"
                    :type="showPw ? 'text' : 'password'"
                    class="form-control"
                    :class="{ 'is-invalid': errors.password }"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password"
                  />
                  <button type="button" class="btn btn-outline-secondary" @click="showPw = !showPw" tabindex="-1">
                    <i :class="showPw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                  <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
                </div>
                <div class="form-text">Must be at least 8 characters with uppercase, lowercase, number, and special character.</div>
              </div>

              <!-- Confirm Password -->
              <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                <input
                  v-model="form.password_confirmation"
                  :type="showPw ? 'text' : 'password'"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password_confirmation }"
                  placeholder="Re-enter password"
                  autocomplete="new-password"
                />
                <div v-if="errors.password_confirmation" class="invalid-feedback">{{ errors.password_confirmation }}</div>
              </div>

              <!-- Actions -->
              <div class="d-flex gap-2 justify-content-end">
                <Link :href="route('admin.users.index')" class="btn btn-light">Cancel</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                  <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-person-check me-1"></i>
                  Create Staff
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  roles:  { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
});

const showPw = ref(false);

const form = useForm({
  name:                  '',
  email:                 '',
  phone:                 '',
  role:                  '',
  transaction_limit:     '',
  status:                'active',
  password:              '',
  password_confirmation: '',
});

const submit = () => form.post(route('admin.users.store'));
</script>
