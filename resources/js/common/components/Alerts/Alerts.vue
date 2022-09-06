<template>
  <div
    v-if="Object.keys(alerts).length"
    class="alerts position-fixed position-bottom-left overflow-scroll font-md px-3 py-4 px-md-5"
    :style="width"
  >
    <alert v-for="(alert, i) in alerts" :key="i" :alert="alert" />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Alert from "./Alert.vue";
export default {
  name: "alerts",
  computed: {
    ...mapGetters({
      alerts: "alerts/alerts",
    }),
    width() {
      return {
        "--width": this.wXS ? "100%" : "28rem",
      };
    },
  },
  components: {
    Alert,
  },
};
</script>

<style lang="scss" scoped>
.alerts {
  top: var(--navbar-height);
  right: 0;
  z-index: 9999;
  width: var(--width);
  max-height: 60%;

  > div:not(:last-of-type) {
    margin-bottom: 0.5em;
  }
}
</style>