<template>
  <PortalLayout title="My Profile" subtitle="Manage your personal information">

    <div class="row g-4">
      <!-- Profile Update -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-semibold">
            <i class="bi bi-person-circle me-2 text-primary"></i>Personal Information
          </div>
          <div class="card-body">
            <div v-if="$page.props.flash?.success" class="alert alert-success small py-2 mb-3">
              <i class="bi bi-check-circle me-1"></i>{{ $page.props.flash.success }}
            </div>

            <!-- Read-only info -->
            <div class="row g-2 mb-4 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
              <div class="col-6">
                <div class="text-muted" style="font-size:.72rem;text-transform:uppercase">Customer #</div>
                <div class="fw-semibold font-monospace small">{{ customer.customer_number }}</div>
              </div>
              <div class="col-6">
                <div class="text-muted" style="font-size:.72rem;text-transform:uppercase">Full Name</div>
                <div class="fw-semibold small">{{ customer.name }}</div>
              </div>
              <div class="col-6">
                <div class="text-muted" style="font-size:.72rem;text-transform:uppercase">Email</div>
                <div class="small">{{ customer.email }}</div>
              </div>
              <div class="col-6">
                <div class="text-muted" style="font-size:.72rem;text-transform:uppercase">Date of Birth</div>
                <div class="small">{{ fmtDate(customer.date_of_birth) }}</div>
              </div>
            </div>

            <form @submit.prevent="profileForm.patch(route('customer.profile.update'))">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Phone</label>
                  <input v-model="profileForm.phone" type="text" class="form-control"
                         :class="profileForm.errors.phone ? 'is-invalid' : ''">
                  <div class="invalid-feedback">{{ profileForm.errors.phone }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">City</label>
                  <input v-model="profileForm.city" type="text" class="form-control"
                         :class="profileForm.errors.city ? 'is-invalid' : ''">
                  <div class="invalid-feedback">{{ profileForm.errors.city }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Address</label>
                  <input v-model="profileForm.address" type="text" class="form-control"
                         :class="profileForm.errors.address ? 'is-invalid' : ''">
                  <div class="invalid-feedback">{{ profileForm.errors.address }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Occupation</label>
                  <input v-model="profileForm.occupation" type="text" class="form-control">
                </div>
                <div class="col-12"><hr class="my-1"><div class="fw-semibold small text-muted">Next of Kin</div></div>
                <div class="col-md-4">
                  <label class="form-label">Name</label>
                  <input v-model="profileForm.next_of_kin_name" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Phone</label>
                  <input v-model="profileForm.next_of_kin_phone" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Relationship</label>
                  <input v-model="profileForm.next_of_kin_relationship" type="text" class="form-control form-control-sm">
                </div>
              </div>
              <button type="submit" class="btn btn-primary mt-3" :disabled="profileForm.processing">
                <span v-if="profileForm.processing" class="spinner-border spinner-border-sm me-1"></span>
                Save Changes
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-semibold">
            <i class="bi bi-shield-lock me-2 text-warning"></i>Change Password
          </div>
          <div class="card-body">
            <form @submit.prevent="pwForm.post(route('customer.profile.password'), { onSuccess: () => pwForm.reset() })">
              <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <input v-model="pwForm.current_password" type="password" class="form-control"
                       :class="pwForm.errors.current_password ? 'is-invalid' : ''">
                <div class="invalid-feedback">{{ pwForm.errors.current_password }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input v-model="pwForm.password" type="password" class="form-control"
                       :class="pwForm.errors.password ? 'is-invalid' : ''">
                <div class="invalid-feedback">{{ pwForm.errors.password }}</div>
                <div class="form-text">Min 8 characters, mixed case + numbers</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input v-model="pwForm.password_confirmation" type="password" class="form-control">
              </div>
              <button type="submit" class="btn btn-warning w-100" :disabled="pwForm.processing">
                <span v-if="pwForm.processing" class="spinner-border spinner-border-sm me-1"></span>
                Change Password
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </PortalLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import PortalLayout from '@/Layouts/PortalLayout.vue'

const props = defineProps({ customer: Object })

const profileForm = useForm({
  phone:                    props.customer.phone          ?? '',
  address:                  props.customer.address        ?? '',
  city:                     props.customer.city           ?? '',
  occupation:               props.customer.occupation     ?? '',
  next_of_kin_name:         props.customer.next_of_kin_name         ?? '',
  next_of_kin_phone:        props.customer.next_of_kin_phone        ?? '',
  next_of_kin_relationship: props.customer.next_of_kin_relationship ?? '',
})

const pwForm = useForm({ current_password: '', password: '', password_confirmation: '' })

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) : '—'
</script>
