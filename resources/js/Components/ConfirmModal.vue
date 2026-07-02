<template>
  <div class="modal fade" :id="id" tabindex="-1" :aria-labelledby="`${id}Label`">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold" :id="`${id}Label`">
            <i :class="`bi ${icon} me-2 text-${variant}`"></i>{{ title }}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-0">{{ message }}</p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button
            type="button"
            class="btn btn-sm"
            :class="`btn-${variant}`"
            data-bs-dismiss="modal"
            @click="$emit('confirmed')"
          >
            {{ confirmLabel }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  id:           { type: String, required: true },
  title:        { type: String, default: 'Confirm Action' },
  message:      { type: String, default: 'Are you sure you want to proceed?' },
  confirmLabel: { type: String, default: 'Confirm' },
  variant:      { type: String, default: 'danger' },
  icon:         { type: String, default: 'bi-exclamation-triangle' },
});
defineEmits(['confirmed']);
</script>
