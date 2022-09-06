<template>
  <client-page :on-created-actions="['business_types/index']">
    <h4 class="mb-4" v-text="$t('XY', { 0: $t('subscribe'), 1: $t('inX', { x: app ? parseName(app.name) : $t('app') }) })" />

    <b-form @submit="submit" @reset="$router.go(-1)">
      <!-- organization -->
      <b-select
        v-if="!$route.query.org_id"
        :class="`col-md-${app ? 12 : 6}`"
        :data="organizations"
        name="client_id"
        attr="organization"
        :errors="errors"
        :cast-text="x => parseName(x)"
        :label="$t('ال') + $t('organization')"
        :null-option-attr="$t('organization')"
        v-model="form.client_id"
      />

      <!-- app -->
      <b-select
        v-if="!$route.query.app_id"
        :class="`col-md-${org ? 12 : 6}`"
        :data="apps"
        name="app_id"
        attr="app"
        :errors="errors"
        :cast-text="x => parseName(x)"
        :label="$t('ال') + $t('app')"
        :null-option-attr="$t('app')"
        v-model="form.app_id"
      />

      <!-- name -->
      <name-input
        class="col-12"
        :errors="errors"
        v-model="form.name"
      />
    
      <!-- domain -->
      <b-form-group v-if="app && app.id == 1" class="col-md-4" :label="$t('domain')">
        <div dir="ltr" class="form-control p-0 overflow-hidden d-flex align-items-center border" :class="{ 'is-invalid' : errors.domain }">
          <p class="bg-light px-2 h-100 pt-2" v-text="'https://'" />
          <b-input class="flex-1" input-class="border-0" placeholder="example" v-model="form.domain" />
          <p class="bg-light px-2 h-100 pt-2" v-text="'.rgbksa.com'" />
        </div>
        <b-error :field="errors.domain" :attr="$t('domain')" />
      </b-form-group>

      <!-- number_of_branches -->
      <b-input
        :class="`col-md-${app && app.id == 1 ? 4 : 6}`"
        :label="$t('number_of_branches')"
        :errors="errors"
        name="number_of_branches"
        attr="number"
        label-class="fs-4"
        v-model="form.number_of_branches"
      />

      <!-- number_of_points_of_sale -->
      <b-input
        :class="`col-md-${app && app.id == 1 ? 4 : 6}`"
        :label="$t('number_of_points_of_sale')"
        :errors="errors"
        name="number_of_points_of_sale"
        attr="number"
        label-class="fs-4"
        v-model="form.number_of_points_of_sale"
      />

      <!-- business type -->
      <b-form-group class="col-12">
        <select-business-type
          class="rounded"
          :class="{ 'border border-danger' : errors.business_type_id }"
          :business-types="business_types"
          v-model="form.business_type_id"
        />
        <b-error :field="errors.business_type_id" :attr="$t('theBusinessType')" />
      </b-form-group>
    </b-form>
  </client-page>
</template>

<script>
import { mapGetters } from "vuex";
import ClientPage from '../../../../../common/masters/Client/ClientPage.vue';
import NameInput from '../../../../../common/components/Inputs/NameInput.vue';
import SelectBusinessType from '../../../../Master/components/SelectBusinessType.vue';
export default {
  name: "organization.apps.create",
  props: {
    id      : { default: null },
    action  : { default: null },
  },
  data() {
    return {
      form: {
        name: null,
        app_id: null,
        domain: null,
        client_id: null,
        business_type_id: null,
        number_of_branches: 1,
        number_of_points_of_sale: 1,
      },
      errors: {},
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      apps: "client/apps",
      organizations: "client/organizations",
      business_types: "business_types/business_types",
    }),
    _id() {
      return this.id || this.$route.params.app_id;
    },
    org_id() {
      return this.$route.query.org_id || null;
    },
    app_id() {
      return this.$route.query.app_id || this.form.app_id || null;
    },
    _action() {
      return this.action || this.$route.params.action || "create";
    },
    org() {
      return this.org_id ? this.organizations[this.org_id] : null;
    },
    app() {
      return this.app_id ? this.apps[this.app_id] : null;
    },
  },
  created: async function () {
    if (this._action == "create") {
      if (this.org_id) {
        await this.$store.dispatch('client/findOrganization', this.org_id)
          .then(org => {
            this.form.client_id = org.id;
            this.form.name = org.name;
          })
          .catch(() => this.$router.push({ name: 'organization.apps.index', params: { org_id: this.org_id } }));
      }
      if (this.app_id) {
        await this.$store.dispatch('client/findApp', this.app_id)
          .then(app => {
            this.form.app_id = app.id;
          })
          .catch(() => this.$router.push({ name: 'organization.apps.index', params: { org_id: this.org_id } }));
      }
    }
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store.dispatch(`client/${this._id ? 'update' : 'create'}ClientApp`, this.form)
        .then(({ id }) => this.$router.push({ name: 'organization.apps.show', params: { id }, query: { org_id: this.org_id } }))
        .catch(errors => this.errors = errors)
      this.loading = false;
    },
  },
  components: {
    ClientPage,
    NameInput,
    SelectBusinessType,
  },
};
</script>