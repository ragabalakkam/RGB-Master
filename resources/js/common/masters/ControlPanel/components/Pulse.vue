<template>
  <span :class="`position-absolute position-${position} pulse bg-${variant}`" :style="cssVars">
    <slot />
  </span>
</template>

<script>
export default {
  name: "pulse",
  props: {
    variant : { default: "danger" },
    size    : { default: "0.39rem" },
    position: { default: "top-right" },
  },
  computed: {
    cssVars() {
      return { '--size': this.size };
    },
  },
};
</script>

<style lang="scss" scoped>
.pulse {
  height: var(--size);
  width: var(--size);
}
</style>