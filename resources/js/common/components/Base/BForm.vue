<template>
  <form
    class="b-form"
    @submit.prevent="$emit('submit')"
    @reset.prevent="$emit('reset')"
  >
    <template v-if="!loading">
      <main class="row">
        <slot />
      </main>

      <footer class="d-flex flex-gap-2 text-capitalize mt-3" v-if="showButtons">
        <b-button type="submit" :variant="submitVariant" v-text="submitText || $t(action)" :disabled="disabled" />
        <b-button type="reset" :variant="cancelVariant" v-text="cancelText || $t('cancel')" :disabled="!disabled" />
      </footer>
    </template>

    <div class="col-12 py-4 d-flex-center" v-else>
      <clip-loader />
    </div>
  </form>
</template>

<script>
export default {
  name: "b-form",
  props: {
    action: { default: "create" },
    showButtons: { default: true },
    loading: { default: false },
    disabled: { default: false },
    submitText: { default: null },
    submitVariant: { default: 'primary' },
    cancelText: { default: null },
    cancelVariant: { default: 'light' },
  },
};
</script>

<style lang="scss" scoped>
.floating-form .b-form > main {
  max-height: 60vh;
  overflow-y: scroll;
  // scroll-behavior: smooth;
  // -ms-overflow-style: none;
  // scrollbar-width: none;

  &::-webkit-scrollbar {
      display: none;
  }
}
</style>