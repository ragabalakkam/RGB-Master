<template>
  <div class="grd h-100 w-100" :style="cssVars" @click="e => $emit('click', e)">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "b-gradient",
  props: {
    dir: {
      default: "bottom",
    },
    from: {
      default: "#000000",
    },
    to: {
      default: "transparent",
    },
  },
  computed: {
    cssVars() {
      return {
        "--dir": this.getLocale() == 'ar'
          ? this.dir.includes("left")
            ? this.dir.replace("left", "right")
            : this.dir.includes("right")
            ? this.dir.replace("right", "left")
            : this.dir
          : this.dir,
        "--from": this.from,
        "--to": this.to,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.grd {
  position: absolute;
  top: 0;
  left: 0;
  background: linear-gradient(to var(--dir), var(--from) 0%, var(--to) 100%);
}
</style>