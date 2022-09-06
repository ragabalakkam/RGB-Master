<template>
  <b-modal v-model="show">
    <!-- header -->
    <template v-slot:header>
      {{ $t("pleaseConfirm") }}
    </template>

    <!-- body -->
    <template v-slot:body>
      {{ $t("logoutMsg") }}
    </template>

    <!-- footer -->
    <template v-slot:footer>
      <b-button variant="secondary" size="sm" @click="cancel" v-t="'logoutCancel'" />
      <b-button variant="danger" size="sm" @click="logout" v-t="'logoutProceed'" />
    </template>
  </b-modal>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "confirm-logout-modal",
  computed: {
    ...mapGetters({
      modals: "modals",
    }),
    modal() {
      return this.modals.logout;
    },
    show: {
      set(value) {
        this.$store.dispatch("setModal", { name: "logout", value });
      },
      get() {
        return this.modal.value;
      },
    },
  },
  methods: {
    logout() {
      this.$store.dispatch("auth/logout").then(() => {

        if(this.$route.path != '/')
          this.smartRedirect('/');

        this.cancel();
      });
    },
    cancel: function () {
      this.show = false;
    },
  },
};
</script>