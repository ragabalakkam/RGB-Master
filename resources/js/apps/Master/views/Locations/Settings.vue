<template>
  <widget :title="$t('xOfy', { x: $t('settings'), y: $t('theLocations') })">
    <form @submit.prevent="onSubmit" @reset.prevent="onReset">
      <!-- api key -->
      <b-input
        :errors="errors"
        name="value"
        attr="googleApiKey"
        v-model="form.value"
        type="password"
        :disabled="disabled"
        :label="$t('googleApiKey')"
      />

      <!-- buttons -->
      <b-form-group>
        <b-button
          class="bg-all-none border-0 text-info p-0"
          v-t="'update'"
          @click="disabled = false"
          v-if="disabled"
        />

        <div v-else>
          <b-button type="submit" variant="primary" v-t="'update'" />
          <b-button type="reset" v-t="'cancel'" />
        </div>
      </b-form-group>
    </form>
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
export default {
  name: "location-settings",
  data() {
    return {
      loading: true,
      form: {
        key: "google_api_key",
        value: null,
      },
      errors: {},
      disabled: true,
    };
  },
  computed: {
    ...mapGetters({
      google_api_key: "configurations/google_api_key",
    }),
  },
  created: async function () {
    this.loading = true;
    await this.$store.dispatch("configurations/show", "google_api_key");
    this.onReset();
    this.loading = false;
  },
  methods: {
    onSubmit: async function () {
      this.loading = true;
      await this.$store
        .dispatch("configurations/update", this.form)
        .then(() => this.onReset())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    onReset: function () {
      this.form.value = `${this.google_api_key}`;
      this.disabled = true;
    },
  },
  components: {
    Widget,
  },
};
</script>

<style>
</style>