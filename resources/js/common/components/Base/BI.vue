<template>
  <i :class="`${fas || `far`} fa-${_icon}${size ? ` fa-${size}` : ''}`" @click="e => $emit('click', e)">
    <slot />
  </i>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "b-i",
  props: ["icon", "size", "fas", "forced"],
  computed: {
    ...mapGetters({
      locales: "locales/locales",
      locale: "locales/locale",
    }),
    dir() {
      const label = this.getLocale();
      const locale = Object.values(this.locales).find((l) => l.label == label);
      return locale ? locale.dir : "ltr";
    },
    _icon() {
      return this.forced || !this.icon
        ? this.icon
        : this.dir === "rtl"
        ? this.icon.includes("-left")
          ? this.icon.replaceAll("left", "right")
          : this.icon.includes("-right")
          ? this.icon.replaceAll("right", "left")
          : this.icon
        : this.icon;
    },
  },
};
</script>

<style>
</style>