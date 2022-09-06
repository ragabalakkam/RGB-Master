<template>
  <widget v-model="loading" :title="$t('ال') + $t('configurations')">
    <multi-tab v-model="page">
      
      <!-- Paths -->
      <tab v-if="form.paths" :name="$t('ال') + $t('paths')">
        <b-form :submit-text="$t('update')" @submit="update('paths')" @reset="reset('paths')" :disabled="disabled('paths')">
          <!-- versions dir -->
          <b-input input-dir="ltr" class="col-12" v-model="form.paths.clients_root_dir" :label="$t('XY', { 0: $t('path'), 1: $t('X', { 0: $t('files'), 1: $t('clients') }) })" />
          
          <!-- clients dir -->
          <b-input input-dir="ltr" class="col-12" v-model="form.paths.versions_root_dir" :label="$t('XY', { 0: $t('path'), 1: $t('X', { 0: $t('files'), 1: $t('versions') }) })" />
          
          <!-- FTP dir -->
          <b-input input-dir="ltr" class="col-12" v-model="form.paths.ftp_dir" :label="$t('XY', { 0: $t('path'), 1: $t('XY', { 0: $t('files'), 1: $t('theBackups') }) })" />
        </b-form>
      </tab>
        
      <!-- Apps -->
      <tab v-if="form.apps" :name="$t('ال') + $t('apps')">
        <b-form :submit-text="$t('update')" @submit="update('apps')" @reset="reset('apps')" :disabled="disabled('apps')">
          <!-- install apps immediately -->
          <b-on-off-input class="col-12" v-model="form.apps.install_apps_immediately">
            {{ $t('installAppsImmediately') }}
          </b-on-off-input>

          <!-- update apps immediately -->
          <b-on-off-input class="col-12" v-model="form.apps.update_apps_immediately">
            {{ $t('updateAppsImmediately') }}
          </b-on-off-input>

        </b-form>
      </tab>

    </multi-tab>
  </widget>
</template>

<script>
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget.vue");
const MultiTab = () => import('../../../../common/vendor/MultiTab/MultiTab.vue');
const Tab = () => import('../../../../common/vendor/MultiTab/Tab.vue');
export default {
  name: "configurations",
  data() {
    return {
      page: 0,
      form: {},
      current: {},
      errors: {},
      loading: true,
    };
  },
  created() {
    ['paths', 'apps'].forEach(key => this.reset(key));
  },
  methods: {
    setConfig: function (key, value) {
      Vue.set(this.form, key, this.obj_clone(value));
      Vue.set(this.current, key, this.obj_clone(value));
    },
    update: async function (key) {
      this.loading = true;
      await this.$store
        .dispatch('configurations/update', { key, value: this.form[key] })
        .then(data => this.setConfig(key, data.value))
        .catch(err => this.errors = err);
      this.loading = false;
    },
    reset: async function (key) {
      this.loading = true;
      if (this.current.hasOwnProperty(key)) {
        Vue.set(this.form, key, this.obj_clone(this.current[key]));
      } else {
        await this.$store
          .dispatch('configurations/show', key)
          .then(data => this.setConfig(key, data.value))
          .catch(err => this.errors = err);
      }
      this.loading = false;
    },
    disabled: function (key) {
      return JSON.stringify(this.form[key]) == JSON.stringify(this.current[key]);
    },
  },
  components: {
    Widget,
    MultiTab,
    Tab,
  },
};
</script>