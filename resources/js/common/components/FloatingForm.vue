<template>
  <div
    v-if="show"
    class="floating-form position-absolute position-top-left min-vh-100 w-100 bg-black-5 d-flex-center index-up"
    :class="{'no-title' : hideTitle}"
    @click="hide"
  >
    <div class="mx-4" :style="`width: ${wXS ? '100%' : '65vw'}`" :class="{'mx-3': wXS}" @click.stop>
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  name: "floating-form",
  props: {
    show      : { default: true },
    hideTitle : { default: false },
  },
  methods: {
    hide: function () {
      this.$emit('hide');
    },
  },
};
</script>

<style lang="scss" scoped>
.index-up {
  z-index: 99999;
}

.floating-form {
  .widget {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }

  &.no-title .widget > header {
    display: none;
  }
}
</style>