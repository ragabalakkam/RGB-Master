<template>
  <div v-if="!loading">
    <slot />
  </div>
  <div v-else class="py-4 text-center bg-white border rounded-lg my-4 shadow">
    <clip-loader />
  </div>
</template>

<script>
export default {
  name: 'b-component',
  props: {
    value: { required: true },
  },
  computed: {
    loading: {
      set(val) {
        this.$emit('input', val);
      },
      get() {
        return this.value;
      },
    },
  },
}
</script>