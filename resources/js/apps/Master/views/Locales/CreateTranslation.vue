<template>
  <div class="create-translation">
    <form @submit="onSubmit" @reset.prevent="close" v-if="!loading">
      <div class="mb-4">
        <!-- key -->
        <div>
          <label for="translation-key" v-text="ucFirst($t('key'))" />
          <b-form-input
            id="translation-key"
            v-model="form.key"
            :class="{ 'is-invalid': errors.key }"
            :disabled="!!translation"
          />
          <b-error :field="errors.key" attr="key" />
        </div>

        <hr />

        <!-- values -->
        <div>
          <p v-text="ucFirst($t('valuesInLocales'))" class="mb-2" />
          <b-form-group v-for="(locale, index) in locales" :key="index">
            <div class="d-flex">
              <label
                :for="`locale-label-${locale.label}`"
                class="p-1 d-flex-center mb-0 mr-2 border rounded-lg"
                :title="$t('valueInLocaleX', { attr: $t(locale.label) })"
              >
                <img
                  :src="`/imgs/flags/${locale.label}.jpg`"
                  class="ht-25 rounded-flag"
                />
              </label>
              <b-form-input
                :id="`locale-label-${locale.label}`"
                v-model="form.values[locale.label]"
                :class="{ 'is-invalid': errors[locale.label] }"
                :dir="locale.dir"
              />
            </div>
            <b-error :field="errors[locale.label]" attr="values" />
          </b-form-group>
        </div>
      </div>

      <!-- buttons -->
      <div class="d-flex">
        <div class="flex-1">
          <b-button variant="primary" type="submit" v-t="action" class="mr-2" />
          <b-button type="reset" v-t="'cancel'" />
        </div>
        <b-button
          v-if="translation"
          variant="light"
          type="button"
          class="border-0 bg-none bg-hover-none text-secondary text-hover-dark p-0"
          :title="$t('delete')"
          @click="destroy"
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
export default {
  name: "create-translation",
  props: {
    translation_key: { required: false },
  },
  mounted() {
    console.log(this.translation_key || "no translation_key selected");
  },
  data() {
    return {
      form: {
        key: "",
        values: {},
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      locales: "locales/locales",
      translations: "locales/translations",
    }),
    translation() {
      return this.translation_key
        ? this.translations[this.translation_key]
        : null;
    },
    action() {
      return this.translation ? "update" : "create";
    },
  },
  mounted() {
    Object.values(this.locales).forEach((locale) =>
      Vue.set(this.form.values, locale.label, "")
    );
    if (this.translation) {
      let values = {};
      Object.entries(this.translations[this.translation_key]).forEach(
        (translation) => (values[translation[0]] = translation[1])
      );
      this.form = { ...this.form, ...{ key: this.translation_key, values } };
    }
  },
  methods: {
    onSubmit: async function () {
      this.loading = true;
      await this.$store
        .dispatch(`locales/${this.action}Translation`, this.form)
        .then(() => this.close())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    destroy: async function () {
      this.loading = true;
      await this.$store
        .dispatch(`locales/deleteTranslation`, this.translation_key)
        .then(() => this.close())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    close: function () {
      this.$emit("close");
    },
  },
};
</script>