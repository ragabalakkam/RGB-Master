<template>
  <div
    :class="`alert btn btn-light btn-block my-2 text-left d-flex align-items-center position-relative text-${variant} shadow`"
    @click="goToLink"
  >
    <div
      :class="`top-line position-absolute position-top-left bg-${variant} d-flex align-items-center`"
      :style="`--time: ${timeout}s`"
    />
    <div class="d-flex-center mr-3">
      <b-i :icon="icon" size="2x" />
    </div>
    <p class="flex-1 ws-pre-wrap mr-3" v-text="$t(`notification_contents.${alert.content}.content`, args) || alert.content" />
    <b-button variant="light" size="sm" class="s-1 p-0 border-0 d-flex-center">
      <b-i icon="times" />
    </b-button>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "alert",
  props: ["alert"],
  computed: {
    ...mapGetters({
      default_timeout: "alerts/default_timeout",
    }),
    timeout() {
      return this.alert.timeout || this.default_timeout;
    },
    variant() {
      return this.alert.variant || "primary";
    },
    icon() {
      if (this.alert.icon) return this.alert.icon;
      switch (this.variant) {
        case "success":
          return "fas fa-check-circle";
        case "danger":
          return "fas fa-times-circle";
        case "info":
          return "fas fa-exclamation-circle";
        case "warning":
          return "fas fa-exclamation-triangle";
      }
      return "check-circle";
    },
    args() {
      let args = this.alert.args,
        casts = this.alert.casts;
      Object.entries(args).forEach(arg => {
        if (casts[arg[0]]) args[arg[0]] = this[casts[arg[0]]](arg[1]);
      });
      return args;
    },
  },
  methods: {
    close: function () {
      this.$store.dispatch("alerts/close", this.alert.id);
    },
    goToLink: function () {
      let link = this.alert.link;
      if (link && typeof link === "string") {
        if (link.includes("https://")) window.open(link);
        else this.$router.push(link);
      }
      this.close(alert.id);
    },
  },
  mounted() {
    setTimeout(() => this.close(), this.timeout * 1000);
  },
};
</script>

<style lang="scss" scoped>
.s-1 {
  width: 1rem;
  height: 1rem;
}

@keyframes desc-line-width {
  0% {
    width: 100%;
  }
  100% {
    width: 0%;
  }
}

.top-line {
  height: 0.125rem;
  animation: desc-line-width var(--time) ease-in-out;
}

.btn-light:hover {
  background-color: var(--bs-white);
}
</style>