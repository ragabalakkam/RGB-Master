<template>
  <div class="multiple-step-form-slide px-5 flex-1">
    <slot />
  </div>
</template>

<script>
export default {
  name: "multiple-step-form-slide",
  props: {
    icon: { type: String, required: false },
    validate: {
      type: Array,
      required: false,
      default: () => {
        [];
      },
    },
    valid: { required: false, default: true },
    nextBtn: { required: false, default: null },
  },
  data() {
    return {
      passed: false,
      skippable: false,
    };
  },
};
</script>

<style>
.multiple-step-form-slide .form-group:last-of-type {
  margin-bottom: 0;
}
</style>