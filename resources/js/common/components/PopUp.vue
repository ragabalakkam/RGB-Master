<template>
  <div
    class="pop-up position-absolute position-top-left min-vh-100 min-vw-100 d-flex-center"
    :class="[
      !popup ? 'closed' : '',
      `pop-up-${(popup ? popup.bgVariant : '') || variant}`,
    ]"
  >
    <div v-if="popup" class="text-center" :class="`text-${variant}`">
      <div
        class="mx-auto rounded-circle border mb-4 h1 d-flex-center"
        :class="`border-${variant} text-${variant}`"
        style="height: 3.5rem; width: 3.5rem; border-width: 0.125rem"
        v-html="
          !popup.icon || popup.icon === 'check'
            ? '&check;'
            : `<i class='fas fa-${popup.icon}' style='font-size: 1.5rem;'></i>`
        "
      ></div>
      <!-- <i class="far fa-check-circle fa-4x mb-3 font-300"></i> -->
      <p :class="`h${wXS ? 2 : 1}`" v-text="popup.msg"></p>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "pop-up",
  computed: {
    ...mapGetters({
      popup: "popup",
    }),
    variant() {
      return this.popup ? this.popup.variant || "success" : "success";
    },
  },
};
</script>

<style lang="scss" scoped>
.pop-up {
  backdrop-filter: blur(4px);
  top: 0;
  transition: var(--bs-duration-m) ease-in-out;
  &.closed {
    top: -100vh;
  }
  > div {
    max-width: 60%;
  }
  // bg color
  $variants: light, danger, success, secondary, info, primary, dark;
  @each $variant in $variants {
    &.pop-up-#{$variant} {
      background: linear-gradient(
        to bottom,
        var(--bs-#{$variant}) 0%,
        transparent 60%
      );
    }
  }
}
</style>