<template>
  <client-page :on-created-actions="['business_types/index']" v-model="loading">
    <template v-if="client_app">

      <div class="d-flex flex-gap-md-bs flex-gap-3" :class="wXS ? 'flex-column' : 'align-items-end'">
        <div class="d-flex align-items-end">
          <b-img class="rounded-circle"
            size="90"
            :src="parseImg(client_app)"
            :title="$t('XY', { 0: $t('app') , 1: parseName(client_app.name) })"
          />
          <b-img
            v-if="app"
            size="40"
            class="rounded-circle"
            style="margin: 0 -1rem;"
            :src="parseImg(app)"
            :title="app ? $t('XY', { 0: $t('app') , 1: parseName(app.name) }) : null"
          />
        </div>
        <div>
          <p v-if="app" class="mb-2" v-text="$t('XY', { 0: $t('app') , 1: parseName(app.name) })" />
          <h4 v-text="parseName(client_app.name)" />
        </div>
      </div>

      <hr class="mb-4" />

      <!-- app info -->
      <div class="row">
        <!-- domain -->
        <div v-if="client_app.domain" class="col-md-4 col-lg-3 mb-3">
          <div :class="`${buttonClass} c-ptr`" block @click="visit(client_app.domain)" :title="$t('X', { 0: $t('open'), 1: $t('app') })">
            <b-i icon="external-link" class="fs-6 wd-25 text-center" />
            <div>
              <p class="font-md mb-1" v-text="ucFirst($t('domain'))" />
              <p class="fs-4 text-dark" v-text="parseName(client_app.domain)" />
            </div>
          </div>
        </div>

        <!-- business type -->
        <div v-if="business_types[client_app.business_type_id]" class="col-md-4 col-lg-3 mb-3">
          <div :class="buttonClass" block>
            <b-i icon="swords-laser" class="fs-6 wd-25 text-center" />
            <div class="flex-1">
              <p class="font-md mb-1" v-text="ucFirst($t('theBusinessType'))" />
                <p class="fs-4 text-dark" v-text="parseName(business_types[client_app.business_type_id].name)" />
            </div>
          </div>
        </div>
        
        <!-- installed_at -->
        <div class="col-md-4 col-lg-3 mb-3">
          <div :class="buttonClass" block>
            <b-i icon="laptop-code" class="fs-6 wd-25 text-center" />
            <div>
              <p class="font-md mb-1" v-text="ucFirst($t('X', { 0: $t('status'), 1: $t('installation') }))" />
              <p class="fs-4 text-dark" v-text="client_app.installed_at ? `${$t('installed')} ${castTime(client_app.installed_at)}` : `${$t('notInstalledYet')} (${$t('pending')})`" />
            </div>
          </div>
        </div>
        
        <!-- licensed_at -->
        <div class="col-md-4 col-lg-3 mb-3">
          <div :class="buttonClass" block>
            <b-i icon="laptop-code" class="fs-6 wd-25 text-center" />
            <div>
              <p class="font-md mb-1" v-text="ucFirst($t('X', { 0: $t('status'), 1: $t('app') }))" />
              <p class="fs-4 text-dark" v-text="client_app.licensed_at ? `${$t('licensed')} ${castTime(client_app.licensed_at)}` : `${$t('beta')} (${$t('notLicensedYet')})`" />
            </div>
          </div>
        </div>

        <!-- created_at -->
        <div class="col-md-4 col-lg-3 mb-3">
          <div :class="buttonClass" block>
            <b-i icon="calendar" class="fs-6 wd-25 text-center" />
            <div>
              <p class="font-md mb-1" v-text="ucFirst($t('timestampX', { attr: $t('ال') + $t('subscribe') }))" />
              <p class="fs-4 text-dark" v-text="castTime(client_app.created_at)" />
            </div>
          </div>
        </div>

        <!-- verrsion -->
        <div v-if="client_app.version_number" class="col-md-4 col-lg-3 mb-3">
          <div :class="buttonClass" block>
            <b-i icon="code-merge" class="fs-6 wd-25 text-center" />
            <div>
              <p class="font-md mb-1" v-text="ucFirst($t('ال') + $t('version'))" />
              <p class="fs-4 text-dark" v-text="parseName(client_app.version_number)" />
            </div>
          </div>
        </div>
      </div>

      <hr class="mb-4 mt-2" />

      <!-- tools -->
      <b-form-group :label="$t('ال') + $t('tools')" label-class="fs-4 mb-3">
        <div class="row">
          <div v-for="(tool, id) in tools" :key="id" class="col-6 col-md-3 col-lg-2 mb-4">
            <b-button
              :variant="tool.vrnt"
              :class="`border-${tool.vrnt && tool.vrnt.includes('outline') ? tool.vrnt.replace('outline-', '') : 'x'} p-3`"
              @click="tool.func(client_app)"
              block
            >
              <b-i :icon="tool.icon" class="d-block fs-5 mb-2" />
              <span v-text="tool.text" />
            </b-button>
          </div>
        </div>
      </b-form-group>

    </template>

    <floating-form v-if="openBusinessTypeForm" @hide="openBusinessTypeForm = false">
      <form class="bg-white rounded-lg p-4" @submit.prevent="update_business_type">
        <b-form-group :label="$t('selectX', { attr: $t('theBusinessType') })" label-class="font-lg">
          <b-select
            class="mb-2"
            :data="business_types"
            :cast-text="x => parseName(x)"
            v-model="client_app.business_type_id"
          />

          <p
            class="mb-3"
            v-text="business_types[client_app.business_type_id].description"
            style="white-space:pre-wrap"
          />

          <div class="d-flex-center flex-gap-3 min-ht-160 mb-3">
            <div v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" class="bg-white py-1 px-2 border">
              <p class="fs-3 mb-1">
                <b-i :icon="device" />
                <span v-t="device" />
              </p>
              <img 
                :src="`/storage/${business_types[client_app.business_type_id].cashier_settings[`${device}_screenshot`]}`"
                v-open-in-viewer
                class="ht-60"
              />
            </div>
          </div>
          
          <b-button variant="primary" type="submit" class="py-1 px-2" v-text="$t('updateX', { attr: $t('theBusinessType') })" />
        </b-form-group>
      </form>
    </floating-form>
  </client-page>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientPage from '../../../../../common/masters/Client/ClientPage.vue';
import FloatingForm from '../../../../../common/components/FloatingForm.vue';
export default {
  names: "organization.apps.show",
  data() {
    return {
      tab: 0,
      client_app: null,
      openBusinessTypeForm: false,
      loading: true,

      // info classes
      buttonClass: 'bg-light-5 bg-hover-light text-secondary-8 text-hover-dark d-flex flex-gap-3 align-items-center h-100 border border-x py-2 px-3',
    };
  },
  computed: {
    ...mapGetters({
      apps: 'client/apps',
      business_types: 'business_types/business_types',
    }),
    id() {
      return this.$route.params.id;
    },
    org_id() {
      return this.$route.query.org_id || (this.client_app ? this.client_app.client_id : null);
    },
    app() {
      return this.apps[this.client_app.app_id];
    },
    tools() {
      let t = this.$t;
      return [
        {
          icon: 'database',
          text: t('takeBackup'),
          vrnt: 'light border',
          func: app => this.export_database(app.id),
        },
        {
          icon: 'layer-group',
          text: t('XY', { 0: t('modify'), 1: t('theBusinessType') }),
          vrnt: 'light border',
          func: app => this.openBusinessTypeForm = true,
        },
        {
          icon: 'bug',
          text: t('ال') + t('reportX', { x: t('aX', { x: t('bug') }) }),
          vrnt: 'outline-danger',
          func: () => this.$store.dispatch('setModal', { name: 'reportProblem', value: true }),
        },
      ];
    },
  },
  mounted: async function() {
    this.loading = true;
    await this.$store
      .dispatch("client/findClientApp", { org_id: this.org_id, id: this.id })
      .then(client_app => {
        console.log({ client_app });
        this.client_app = client_app;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "organization.apps.index", params: { org_id: this.org_id }}));
    this.loading = false;
  },
  methods: {
    exec: async function (callback) {
      this.loading = true;
      await callback();
      this.loading = false;
    },
    export_database: function (id) {
      this.exec(() => this.$store
        .dispatch('client/export_app_database', { id })
        .then(src => this.downloadURI(`/storage/${src}`))
        .catch(err => console.log({ err }))
      );
    },
    update_business_type: function () {
      this.exec(() => this.$store
        .dispatch('client/update_app_business_type', {
          id: this.client_app.id,
          business_type_id: this.client_app.business_type_id,
          configurations: [],
          update_configurations: false,
          update_database: false
        })
        .then(() => this.openBusinessTypeForm = false)
        .catch(err => console.log({ err }))
      );
    },
  },
  components: {
    ClientPage,
    FloatingForm
  },
};
</script>
