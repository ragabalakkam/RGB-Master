<template>
  <div class="create-locale">
    <!-- success message -->
    <p
      v-if="action == 'update' && message && !loading"
      class="bg-success-8 text-white py-2 px-3 d-flex align-items-center rounded-lg mb-4 c-ptr bg-hover-success"
      @click="message = null"
    >
      <b-i icon="check-circle" class="mr-2 font-300" />
      <span v-text="ucFirst($t(message))" />
    </p>

    <!-- form -->
    <form @submit.prevent="onSubmit" @reset.prevent="close" v-show="!loading">
      <div>
        <!-- name -->
        <name-input v-model="form.name" :errors="errors" />

        <b-form-group class="d-flex flex-gap-bs">
          <!-- flag -->
          <b-form-group class="wd-50" :label="$t('ال') + $t('flag')">
            <img
              v-if="existing_labels.includes(form.label)"
              :src="`/imgs/flags/${form.label}.jpg`"
              class="w-100 rounded"
            />
            <b-img-input
              v-else
              v-model="form.flag"
              :class="{ 'is-invalid': errors.flag }"
            />
          </b-form-group>

          <!-- label -->
          <b-input
            class="wd-60"
            name="label"
            :errors="errors"
            :showError="false"
            :label="$t('ال') + $t('label')"
            v-model="form.label"
          />

          <!-- dir -->
          <b-select
            class="flex-1"
            name="dir"
            :label="$t('dir')"
            :errors="errors"
            v-model="form.dir"
            :options="selectDirOptions"
          />
        </b-form-group>

        <b-error :field="errors.flag" attr="flag" />
      </div>

      <!-- Options ( export / import backups / set as default ) -->
      <div class="my-4 text-secondary" v-if="action == 'update'">
        <!-- export -->
        <button
          type="button"
          class="btn bg-none btn-block text-left px-0 py-1 no-shadow text-inherit"
          @click="backup('export')"
        >
          <b-i icon="file-export" class="wd-25 mr-1 text-center" />
          <span v-text="ucFirst($t('takeBackup'))" />
        </button>
        <!-- import -->
        <button
          type="button"
          class="btn bg-none btn-block text-left px-0 py-1 no-shadow text-inherit"
          @click="backup('import')"
        >
          <b-i icon="cloud-download-alt" class="wd-25 mr-1 text-center" />
          <span v-text="ucFirst($t('restoreBackup'))" />
        </button>
        <!-- set as default -->
        <button
          type="button"
          class="btn bg-none btn-block text-left px-0 py-1 no-shadow text-hover-info"
          :class="{ 'text-primary': isDefault }"
          @click="setAsDefault"
        >
          <b-i
            :icon="isDefault ? 'check-circle' : 'circle'"
            class="wd-25 mr-1 text-center"
          />
          <span v-text="ucFirst($t('setDefaultLocale'))" />
        </button>
      </div>

      <!-- buttons -->
      <div class="d-flex">
        <div class="flex-1">
          <b-button variant="primary" type="submit" v-t="action" class="mr-2" />
          <b-button type="reset" v-t="'cancel'" />
        </div>
        <b-button
          v-if="locale"
          variant="light"
          type="button"
          class="border-0 bg-none bg-hover-none text-secondary text-hover-dark p-0"
          :title="$t('delete')"
          @click="
            confirmDelete(
              locale,
              'locales/deleteLocale',
              null,
              null,
              () => close
            )
          "
        >
          <b-i icon="trash-alt" />
        </b-button>
      </div>
    </form>

    <!-- loading -->
    <clip-loader v-show="loading" />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const NameInput = () => import("../../../../common/components/Inputs/NameInput");
export default {
  name: "create-locale",
  props: {
    id: { required: false },
  },
  data() {
    return {
      form: {
        name: {},
        label: null,
        dir: "ltr",
        flag: null,
      },
      errors: {},
      message: null,
      loading: false,
      existing_labels: ["en", "ar", "fr", "gr", "sp", "ru", "in"],
      selectDirOptions: [
        { text: this.$t("ltr"), value: "ltr" },
        { text: this.$t("rtl"), value: "rtl" },
      ],
    };
  },
  computed: {
    ...mapGetters({
      locales: "locales/locales",
      default_locale: "configurations/locale",
    }),
    locale() {
      return this.id ? this.locales[this.id] : null;
    },
    action() {
      return this.locale ? "update" : "create";
    },
    isDefault() {
      return this.locale && this.default_locale == this.locale.label;
    },
  },
  mounted() {
    if (this.id) this.form = { ...this.form, ...this.locale };
  },
  methods: {
    onSubmit: async function () {
      this.loading = true;
      await this.$store
        .dispatch(`locales/${this.action}Locale`, this.form)
        .then(() => this.close())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    destroy: async function () {
      this.loading = true;
      await this.$store
        .dispatch("locales/deleteLocale", this.locale.id)
        .then(() => this.close())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    close: function () {
      this.$emit("close");
    },
    backup: async function (action) {
      this.loading = true;
      if (this.locale) {
        await this.$store.dispatch("locales/backup", {
          label: this.locale.label,
          action,
        });
        this.message = `${action}edLocale`;
      }
      this.loading = false;
    },
    setAsDefault: async function () {
      this.loading = true;
      await this.$store
        .dispatch("locales/setAsDefault", this.locale.label)
        .then(() => this.close())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
  },
  watch: {
    "form.name": {
      handler: function ({ en }) {
        if (this.action !== "create" || !en) return;
        en = en.substr(0, 2).toLowerCase();
        this.form.label = this.existing_labels.includes(en) ? en : null;
      },
      deep: true,
    },
  },
  components: {
    NameInput,
  },
};
</script>