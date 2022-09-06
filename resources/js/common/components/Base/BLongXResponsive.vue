<template>
  <div class="b-long-x-responsive" :style="cssVars">
    <div class="h-100">
      <slot v-show="!loading" />
      <div v-show="loading" class="h-100 d-flex-center">
        <clip-loader />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "b-long-x-responsive",
  props: {
    width: { default: 800 },
    sizeXs: { default: null },
  },
  data() {
    return {
      parentWidth: 300,
      loading: true,
    };
  },
  computed: {
    cssVars() {
      return {
        "--responsive-width": `${this.width}px`,
        "--width": `${this.parentWidth}px`,
      };
    },
  },
  methods: {
    refresh: function () {
      this.loading = true;
      setTimeout(() => {
        this.parentWidth =
          this.wXS && this.sizeXs
            ? this.sizeXs
            : this.$el.parentElement.clientWidth;
        this.loading = false;
      }, 200);
    },
  },
  watch: {
    windowWidth: "refresh",
  },
};
</script>

<style lang="scss" scoped>
@media (max-width: 576px) {
  .b-long-x-responsive {
    width: var(--width);
    overflow-x: scroll;

    > div {
      width: var(--responsive-width);
    }
  }
}
</style>