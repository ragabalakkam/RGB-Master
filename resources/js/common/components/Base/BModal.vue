<template>
  <div
    v-if="show"
    @click="show = false"
    class="modal position-absolute position-top-left min-vh-100 vw-100 bg-black-9 bd-blur-3 p-4 d-flex-center"
  >
    <div class="bg-white rounded-lg p-4" @click.stop>
      <!-- header -->
      <div class="d-flex align-items-center flex-gap-2">
        <div class="flex-1 font-xl">
          <slot name="header" />
        </div>
        <b-button
          @click="show = false"
          class="p-1 bg-all-none border-0 text-secondary-5 text-hover-secondary no-shadow"
        >
          <b-i icon="times" />
        </b-button>
      </div>

      <!-- body -->
      <div class="my-4 position-relative">
        <slot name="body" />
      </div>

      <!-- footer -->
      <div dir="ltr" class="d-flex flex-gap-2">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Modal",
  props: {
    value: { required: true },
  },
  computed: {
    show: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
  },
};
</script>


<style lang="scss" scoped>
.modal {
  z-index: 99999;
}

.pc-view,
.tablet-view {
  .modal > div {
    min-width: 25rem;
  }
}

.phone {
  .modal > div {
    width: 100%;
  }
}
</style>