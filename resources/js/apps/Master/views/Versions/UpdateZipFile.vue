<template>
  <widget
    v-model="loading"
    :on-created-actions="['versions/index']"
    :title="$t('updateX', { attr: $t('XY', { 0: $t('file'), 1: `( ${filename} )`}) })"
  >
    <b-form :submit-text="$t('update')" @submit="update" @reset="$router.push('/master/versions')">
      <!-- file -->
      <b-form-group class="col-12" :label="$t('ال') + $t('file')">
        <b-file-input v-model="form.file" accept=".zip" :class="{ 'is-invalid' : errors.file }" />
        <b-error v-if="errors && errors.file" :field="errors.file" attr="" />
      </b-form-group>
    </b-form>
  </widget>
</template>

<script>
const Widget = () => import('../../../../common/masters/ControlPanel/components/Widget.vue');
const BError = () => import('../../../../common/components/Base/BError.vue');
export default {
  name: "versions.create",
  data() {
    return {
      page: 0,
      form: {
        name: null,
        file: null,
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    filename() {
      let name = this.$route.params.file;
      this.form.name = name;
      return name;
    },
  },
  methods: {
    update: async function () {
      this.loading = true;
      await this.$store
        .dispatch('versions/updateFile', this.form)
        .then(data => this.$router.push('/master/versions'))
        .catch(err => this.errors = err);
      this.loading = false;
    },
  },
  components: {
    Widget,
    BError,
  },
};
</script>