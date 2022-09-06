<template>
  <create-page
    @submit="({ id }) => $router.push({ name: 'clients.show', params: { id } })"
    @reset="$router.push({ name: 'clients.index' })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :title="createOrUpdate('client')"
    :on-created-actions="['business_types/index', 'versions/index']"
    module="clients"
    v-model="form"
  >
    <multi-tab class="col-12" v-model="page">

      <!-- organization info -->
      <tab :name="$t('X', { 0: $t('data'), 1: $t('organization') })">
        <div class="row">
          <!-- logo -->
          <b-form-group class="col-12" :label="$t('ال') + $t('logo')" >
            <b-img-input class="ht-220" v-model="form.image" :src="parseImg(form)" />
          </b-form-group>

          <!-- name -->
          <multi-lang-input :errors="errors" :name="$t('ال') + $t('name')" attr="name" class="col-12" no-resize v-model="form.name" />

          <!-- slogan -->
          <multi-lang-input :errors="errors" :name="$t('ال') + $t('slogan')" attr="slogan" class="col-12" v-model="form.slogan" :rows="2" />

          <!-- tax number -->
          <b-input
            class="col-md-6"
            name="tax_number"
            attr="theTaxNumber"
            :attrs="{ value: 15 }"
            :errors="errors"
            :placeholder="`3${'x'.repeat(9)}00003`"
            :label="$t('theTaxNumber')"
            v-model="form.tax_number"
          />

          <!-- commercial registration number -->
          <b-input
            class="col-md-6"
            name="commercial_reg_no"
            attr="theCommercialRegNo"
            :attrs="{ value: 15 }"
            :errors="errors"
            :placeholder="`${'x'.repeat(10)}`"
            :label="$t('theCommercialRegNo')"
            v-model="form.commercial_reg_no"
          />

          <!-- phone -->
          <phone-input class="col-md-6" :errors="errors" v-model="form.phone" />

          <!-- email -->
          <b-input class="col-md-6 mb-3" type="email" name="email" attr="email" :errors="errors" v-model="form.email" />
          
          <!-- address -->
          <address-input class="col-12" v-model="form.address" />

          <!-- notes -->
          <b-textarea class="col-12" rows="3" v-model="form.notes" :errors="errors" :label="$t('ال') + $t('notes')" />
        </div>
      </tab>

      <template v-if="_action == 'create'">

        <!-- apps -->
        <tab :name="$t('ال') + $t('apps')">
          <div class="row">
            <div
              v-for="app in Object.values(apps).filter(a => form.apps.hasOwnProperty(a.id))"
              :key="app.id"
              class="col-md-4 mb-3"
              @click="toggleApp(app)"
            >
              <div
                class="p-3 p-md-4 d-flex flex-gap-2 flex-gap-md-4 align-items-center rounded-lg border c-ptr"
                :class="form.apps[app.id].on ? 'bg-primary text-white' : 'bg-light-2 bg-hover-light'"
              >
                <div class="flex-1">
                  <b-on-off-input class="mb-3 text-inherit" v-model="form.apps[app.id].on" on-color="white" off-color="secondary" @click.stop>
                    {{ parseName(app.name) }}
                  </b-on-off-input>
                  <p class="fs-3 text-inherit" v-html="abbr(parseName(app.description), wXS ? 130 : 200)" :title="parseName(app.description)" />
                </div>
                <b-img :src="parseImg(app)" :size="wXS ? 90 : 120" class="rounded-lg" />
              </div>
            </div>
          </div>
        </tab>

        <!-- configurations -->
        <tab
          v-for="app in Object.values(form.apps).filter(a => a.on)"
          :key="app.app_id"
          :name="$t('XY', { 0: $t('app'), 1: parseName(apps[app.app_id].name) })"
        >
          <!-- version & files -->
          <b-form-group
            :label="`${$t('ال') + $t('files')} ${$t('and')} ${$t('ال') + $t('version')}`"
            label-class="bg-primary text-light px-2 py-1 rounded-bottom mb-3"
            class="mb-4 border-top border-primary"
          >
            <div class="row">
              <!-- demo / offline / live -->
              <template v-if="app.configurations">
                <b-on-off-input
                  v-for="flag in ['demo', 'offline', 'live'].filter(k => app.configurations.hasOwnProperty(k))"
                  :key="flag"
                  class="col-12"
                  v-model="form.apps[app.app_id].configurations[flag]"
                  :errors="errors"
                  :name="flag"
                >
                  <div v-html="$t(`${flag}App`)" />
                </b-on-off-input>
              </template>

              <!-- domain -->
              <b-input
                v-if="app.configurations.live"
                class="col-12"
                input-dir="ltr"
                v-model="form.apps[app.app_id].domain"
                :label="$t('domain')"
                placeholder="https://example.rgbksa.com"
              />

              <!-- root dir -->
              <b-form-group class="col-12" :label="$t('X', { 0: $t('path'), 1: $t('files') })">
                <b-labeled-input
                  name="root_dir"
                  attr="root_dir"
                  :errors="errors"
                  v-model="form.apps[app.app_id].root_dir"
                  :label="`${paths.clients_root_dir}/`"
                  label-class="fs-4"
                  dir="ltr"
                />
              </b-form-group>

              <!-- version -->
              <b-select
                class="col-12"
                :label="$t('ال') + $t('version')"
                :data="Object.values(versions).filter(v => v.type != 'patch' && v.app_id == app.app_id)"
                :errors="errors"
                txt="number"
                name="version_id"
                attr="version"
                :null-option-attr="$t('version')"
                v-model="form.apps[app.app_id].version_id"
              />

              <!-- business type -->
              <select-business-type
                class="col-12"
                v-model="form.apps[app.app_id].business_type_id"
                :business-types="business_types"
                @change="changeBusinessType(app.app_id, form.apps[app.app_id].business_type_id)"
              />

              <!--   -->
            </div>
          </b-form-group>

          <!-- database -->
          <b-form-group
            :label="$t('theDatabase')"
            class="mb-4 border-top border-primary"
            label-class="bg-primary text-light px-2 py-1 rounded-bottom mb-3"
          >
            <div class="row">
              <!-- database drive -->
              <b-select
                class="col-md-6"
                :label="$t('X', { 0: $t('type'), 1: $t('theDatabase') })"
                :data="[{ name: 'MySQL', id: 'mysql' }, { name: 'MS SQL Server', id: 'sqlsrv' }]"
                v-model="form.apps[app.app_id].db_driver"
                :errors="errors"
                name="db_driver"
                attr="driver"
                :disabled="true"
              />

              <!-- database host -->
              <b-input
                class="col-md-6"
                :label="$t('host')"
                name="db_host"
                attr="host"
                :errors="errors"
                v-model="form.apps[app.app_id].db_host"
                :disabled="true"
              />

              <!-- database name -->
              <b-input
                class="col-12"
                :label="$t('XY', { 0: $t('name'), 1: $t('theDatabase') })"
                :errors="errors"
                name="db_database"
                attr="name"
                label-class="fs-4"
                v-model="form.apps[app.app_id].db_database"
                :placeholder="`example : rgbksaco_client_db`"
              />

              <!-- database username -->
              <b-input
                class="col-12"
                :label="$t('username')"
                :errors="errors"
                name="db_username"
                attr="username"
                label-class="fs-4"
                v-model="form.apps[app.app_id].db_username"
                :placeholder="`example : rgbksaco_client_user`"
              />

              <!-- database password -->
              <b-password-input
                v-if="false"
                class="col-12"
                :label="$t('password')"
                name="db_password"
                attr="password"
                :errors="errors"
                v-model="form.apps[app.app_id].db_password"
              />
            </div>
          </b-form-group>

          <!-- organization -->
          <b-form-group
            v-if="['number_of_branches','number_of_points_of_sale'].filter(prop => app.configurations.hasOwnProperty(prop)).length"
            :label="$t('ال') + $t('organization')"
            class="mb-4 border-top border-primary"
            label-class="bg-primary text-light px-2 py-1 rounded-bottom mb-3"
          >
            <div class="row">

              <!-- number_of_branches -->
              <b-input
                class="col-md-6"
                :label="$t('number_of_branches')"
                :errors="errors"
                name="number_of_branches"
                attr="number"
                label-class="fs-4"
                v-model="form.apps[app.app_id].configurations.number_of_branches"
              />

              <!-- number_of_points_of_sale -->
              <b-input
                class="col-md-6"
                :label="$t('number_of_points_of_sale')"
                :errors="errors"
                name="number_of_points_of_sale"
                attr="number"
                label-class="fs-4"
                v-model="form.apps[app.app_id].configurations.number_of_points_of_sale"
              />

            </div>
          </b-form-group>

          <!-- modules -->
          <b-form-group
            v-if="appsConfigurations[app.app_id].modules"
            :label="$t('ال') + $t('modules')"
            label-class="bg-primary text-light px-2 py-1 rounded-bottom mb-3"
            class="mb-4 border-top border-primary"
          >
            <!-- <b-input v-model="search_modules" :placeholder="$t('searchByX', { x: $t('X', [$t('name'), $t('module')]) })" /> -->

            <hr>

            <div class="row">
              <div class="col-md-4" v-for="(value, name) in appsConfigurations[app.app_id].modules" :key="name">
                <b-on-off-input v-model="form.apps[app.app_id].configurations[name]" size="lg">
                  <span
                    v-if="appsConfigurations[app.app_id] && appsConfigurations[app.app_id].modules[name]"
                    v-html="parseName(appsConfigurations[app.app_id].modules[name].name)"
                  />
                </b-on-off-input>
                <hr class="my-2" />
              </div>
            </div>
          </b-form-group>
        </tab>
        
      </template>

    </multi-tab>
  </create-page>
</template>

<script>
import { mapGetters } from 'vuex';
import SelectBusinessType from '../../components/SelectBusinessType.vue';
import CreatePage from "../../../../common/masters/ControlPanel/pages/CreatePage";
import MultiTab from '../../../../common/vendor/MultiTab/MultiTab.vue';
import Tab from '../../../../common/vendor/MultiTab/Tab.vue';
import PhoneInput from '../../../../common/components/Inputs/PhoneInput.vue';
import MultiLangInput from '../../../../common/components/Inputs/MultiLangInput.vue';
import AddressInput from '../../../../common/components/Inputs/AddressInput.vue';
export default {
  name: "clients.create",
  props: {
    id      : { default : null },
    action  : { default : null },
  },
  data() {
    return {
      page: 0,
      form: {
        image: null,
        name: null,
        slogan: null,
        tax_number: null,
        commercial_reg_no: null,
        phone: null,
        email: null,
        address: [null, null, null, null, null, null, null],
        notes: null,
        extra: null,
        apps: {},
      },
      errors: {},
      loading: true,
      search_modules: null,
    };
  },
  computed: {
    ...mapGetters({
      paths: "configurations/paths",
      apps: 'apps/apps',
      versions: 'versions/versions',
      business_types: 'business_types/business_types',
    }),
    _id() {
      return this.id || this.$route.params.id;
    },
    _action() {
      return this.action || this.$route.params.action || 'create';
    },
    filtered_mods() {
      let modules = null;
      try { modules = this.form.apps[1].configurations.modules; } catch (e) {}
      if (!modules) return [];

      let t = this.$t,
        s = this.search_modules;
      if (s) s = s.toLowerCase();
      return Object.keys(modules).filter(m => !s || (m.startsWith('print_report.') ? t('xOfy', { x: t('reports'), y: t(m)}) : t(`master_modules.${m}`)).toLowerCase().includes(s))
    },
    appsConfigurations() {
      let configs = {};
      Object.values(this.apps).forEach(app => {
        configs[app.id] = {};
        app.configuration_groups.forEach(gr => {
          configs[app.id][gr.key] = {};
          gr.configurations.forEach(c => {
            configs[app.id][gr.key][c.key] = c;
          });
        });
      });
      return configs;
    },
  },
  created: async function () {
    if (this._action == 'create')
    {
      this.loading = true;
      await this.$store.dispatch('configurations/show', 'paths');
      await this.$store.dispatch('apps/index')
        .then(apps => apps.forEach(app => this.toggleApp(app, false)));
      this.loading = false;
    }
  },
  methods: {
    toggleApp: function(app, on = true) {
      if (!this.form.apps.hasOwnProperty(app.id)) {
        Vue.set(this.form.apps, app.id, {
          app_id: app.id,
          on,

          version_id: null,
          business_type_id: null,

          domain: 'https://example.rgbksa.com',
          root_dir: 'example',

          db_driver : 'mysql',
          db_host : 'localhost',
          db_database : 'rgbksaco_rgb_example',
          db_username : 'rgbksaco_rgb_example',
          db_password : 'afaqrgb2000',

          configurations: {},
        });
        app.configuration_groups.forEach(group => {
          group.configurations.forEach(config => Vue.set(this.form.apps[app.id].configurations, config.key, config.default));
        });
      }
      else Vue.set(this.form.apps[app.id], 'on', !this.form.apps[app.id].on);
    },
    changeBusinessType: function (app_id, business_type_id) {
      let app = this.apps[app_id],
        business_type = this.business_types[business_type_id] || { cashier_settings: {} },
        configurations = {};

      Object.values(app.configuration_groups.reduce((acc, cur) => ({ ...acc, ...cur.configurations }), []))
        .map(config => config.key)
        .forEach(k => configurations[k] = null);

      Vue.set(this.form.apps[app_id], 'configurations', {
        ...configurations,
        ...this.form.apps[app_id].configurations,
        ...business_type.modules,
        ...business_type.cashier_settings
      });
    },
  },
  components: {
    CreatePage,
    MultiTab,
    Tab,
    PhoneInput,
    MultiLangInput,
    AddressInput,
    SelectBusinessType,
  },
};
</script>