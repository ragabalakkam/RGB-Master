<template>
  <b-modal v-model="show">
    <!-- header -->
    <template v-slot:header>
      {{ $t("pleaseConfirm") }}
    </template>

    <!-- body -->
    <template v-slot:body>
      {{ $t("deleteConfirmation.msg", { attr: parseName(attr) }) }}

      <!-- loading -->
      <div
        v-show="loading"
        class="position-absolute position-top-left h-100 w-100 d-flex-center bg-white-8"
      >
        <clip-loader size="1.5rem" class="pulse" />
      </div>
    </template>

    <!-- footer -->
    <template v-slot:footer>
      <b-button
        variant="secondary"
        size="sm"
        v-t="'deleteConfirmation.cancel'"
        @click="hide"
        :disabled="loading"
      />
      <b-button
        variant="danger"
        size="sm"
        v-t="'deleteConfirmation.proceed'"
        @click="confirm"
        :disabled="loading"
      />
    </template>
  </b-modal>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "delete-confirmation-modal",
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      modals: "modals",
    }),
    modal() {
      return this.modals.delete;
    },
    model() {
      return this.modal.model || {};
    },
    action() {
      return this.modal.action || null;
    },
    attr() {
      return this.modal.attr || this.model.name || "";
    },
    show: {
      set(value) {
        this.$store.dispatch("setModal", {
          name: "delete",
          value,
          data: this.modal.data,
        });
      },
      get() {
        return this.modal.value;
      },
    },
  },
  methods: {
    confirm: async function () {
      this.loading = true;
      await this.$store
        .dispatch(this.action, { id: this.model.id, ...this.modal.params })
        .then(() => {
          this.modal.callback();
          const route = this.$route.name;
          if (
            route.includes("show") &&
            this.$router.resolve({ name: route.replace("show", "index") })
          )
            this.$router.replace({ name: route.replace("show", "index") });
        });
      this.loading = false;
      this.hide();
    },
    hide: function () {
      this.show = false;
    },
    setShow: function () {
      this.show = !!(this.model.id && this.action);
    },
  },
};
</script>