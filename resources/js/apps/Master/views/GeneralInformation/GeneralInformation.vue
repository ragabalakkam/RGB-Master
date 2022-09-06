<template>
  <widget :title="$t('identificationCard')">
    <b-form
      action="update"
      :loading="loading"
      :disabled="disabled"
      @submit="update"
      @reset="reset"
    >
      <!-- logo -->
      <b-form-group class="col-12" :label="$t('ال') + $t('logo')">
        <b-img-input
          class="ht-280 bg-light"
          img-class="h-100"
          :src="`/storage/${form.logo}`"
          v-model="form.logo"
        />
      </b-form-group>

      <!-- app name -->
      <name-input class="col-12" :errors="errors" v-model="form.app_name" />
    </b-form>
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const NameInput = () => import("../../../components/Inputs/NameInput");
const Widget = () => import( "../../../masters/ControlPanel/components/Widget");
export default {
  name: "general-information",
  data() {
    return {
      form: {
        app_name: {},
        logo: null,
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      app: "configurations/general_information",
    }),
    disabled() {
      return JSON.stringify(this.form) == JSON.stringify(this.app);
    },
  },
  mounted: async function () {
    this.loading = true;
      await this.$store
        .dispatch("configurations/show", 'general_information')
        .catch((errors) => (this.errors = errors));
    this.loading = false;
  },
  methods: {
    update: async function () {
      this.loading = true;
      await this.$store
        .dispatch("configurations/setGeneralInformation", this.form)
        .then(() => this.reset())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    reset: function () {
      this.form = this.obj_clone(this.app);
    },
  },
  watch: {
    app: {
      handler: "reset",
      immediate: true,
    },
  },
  components: {
    Widget,
    NameInput,
  },
};
</script>