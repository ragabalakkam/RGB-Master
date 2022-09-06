<template>
  <div
    class="h-100 d-flex flex-column overflow-hidden"
    :class="wXS ? 'phone-view' : wSM ? 'tablet-view' : 'pc-view'"
  >
    <template v-if="!loading">
      <!-- conncetion lost / back online -->
      <div
        v-if="!online || backOnline"
        class="text-light font-bold text-center py-2"
        :class="backOnline ? 'bg-success' : !online ? 'bg-danger' : null"
        v-t="!online ? 'disconnected' : 'reconnected'"
      />

      <!-- email address not verified -->
      <resend-email-verification-link v-if="user && !emailVerified" />

      <router-view />

      <!-- image viewer -->
      <image-viewer />

      <!-- alerts -->
      <alerts />

      <!-- pop-up -->
      <pop-up />

      <!-- inputs -->
      <div
        v-if="current_select"
        class="position-fixed posiiton-top-left min-vh-100 w-100 index-up"
        :class="`bg-dark-${wXS ? 5 : 2}`"
        @click="
          current_select && current_select.id
            ? $store.commit('SET_INPUT', {
                name: 'select',
                id: current_select.id,
                value: { ...current_select, focused: false },
              })
            : null
        "
      >
        <b-select-form class="position-absolute" @click.stop />
      </div>

      <!-- modals -->
      <delete-confirmation-modal />
      <confirm-logout-modal />
      <report-problem-modal :online="online" />
    </template>

    <!-- loader : top most overlay when loading -->
    <loading v-show="loading" />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const ResendEmailVerificationLink = () => import("./components/ResendEmailVerificationLink");
const ImageViewer = () => import("./components/ImageViewer");
const PopUp = () => import("./components/PopUp");
const Alerts = () => import("./components/Alerts/Alerts");
const DeleteConfirmationModal = () => import("./modals/DeleteConfirmationModal");
const ConfirmLogoutModal = () => import("./modals/ConfirmLogoutModal");
const ReportProblemModal = () => import('../apps/Client/modals/ReportProblemModal.vue');
const Loading = () => import("./components/Loading.vue");
const BSelectForm = () => import("./components/Inputs/Select/BSelectForm.vue");
export default {
  name: "app",
  computed: {
    ...mapGetters({
      user: "auth/user",
      emailVerified: "auth/emailVerified",
      loading: "loading",
      errors: "errors",
      locale: "locales/locale",
      app: "configurations/app",
      account_id: "account_id",
      select: "select",
      fullscreen_mode: "fullscreen",
    }),
    fullscreen: {
      get() {
        return this.fullscreen_mode;
      },
      set(val) {
        this.setFullscreen(val);
      },
    },
    current_select() {
      return Object.values(this.select).find((inp) => inp.focused);
    },
  },
  data() {
    return {
      online: true,
      backOnline: false,
    };
  },
  mounted() {
    // Init
    this.$store.dispatch("init");
    // Detect online / offline status
    window.addEventListener("online", () => (this.online = true));
    window.addEventListener("offline", () => (this.online = false));
  },
  methods: {
    updateTitle: function () {
      if (!this.loading) {
        let routename = this.$route.name;

        if (!routename) return this.parseName(this.app.name);
        if (routename.startsWith("master-")) routename = routename.substr(7);

        document.title = `${this.$t("routes." + routename)} | ${this.parseName(this.app.name)}`;
      } else setTimeout(() => this.updateTitle(), 200);
    },
  },
  watch: {
    online: function (newStatus, oldStatus) {
      if (!oldStatus && newStatus) {
        this.backOnline = true;
        setTimeout(() => (this.backOnline = null), 1000);
      }
    },
    fullscreen: function (val = true) {
      try {
        let elem = document.querySelector("html");
        if (val) {
          if (elem.requestFullscreen) {
            elem
              .requestFullscreen()
              .catch((err) =>
                console.log(`Cannot enter full-screen mode`, err)
              );
          } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
          } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
          }
        } else {
          document
            .exitFullscreen()
            .catch((err) => console.log(`Cannot exit full-screen mode`, err));
        }
      } catch (e) {}
    },
    windowHeight: {
      handler: function (newVal) {
        if (newVal === screen.availHeight) this.fullscreen = true;
      },
      immediate: true,
    },
    locale: {
      handler: "updateTitle",
      immediate: true,
    },
    "$route.fullPath": "updateTitle",
  },
  components: {
    ResendEmailVerificationLink,
    ImageViewer,
    PopUp,
    Alerts,
    DeleteConfirmationModal,
    ConfirmLogoutModal,
    ReportProblemModal,
    Loading,
    BSelectForm,
  },
};
</script>

<style lang="scss">
html[dir="ltr"] body > div.sticky-sidebar {
  .pc-view,
  .tablet-view {
    padding-left: 5rem;
  }
}
html[dir="rtl"] body > div.sticky-sidebar {
  .pc-view,
  .tablet-view {
    padding-right: 5rem;
  }
}

.rounded-flag {
  border-radius: 0.125rem;
}
.loading {
  z-index: 9999;
}

select::placeholder,
textarea::placeholder,
input::placeholder {
  color: var(--bs-placeholder) !important;
}

legend {
  font-size: 0.9rem !important;
}

form {
  margin-block-end: 0;
}

/* id field | temporary */

.tablet-view,
.pc-view {
  .id-field {
    width: 3rem;
  }

  .actions-field {
    width: 4.5rem;
  }

  .img-field {
    width: 5rem;
  }
}

.phone-view {
  .id-field {
    display: none;
  }

  .actions-field {
    width: 4.6875rem;
  }

  .img-field {
    width: 4.6875rem;
  }
}

/* preparing new theme variables */

:root {
  --bs-placeholder: rgb(207, 207, 207);

  --loading-from: var(--bs-white);
  --loading-to: var(--bs-info);
  --loader-color: var(--bs-primary);
}
</style>
