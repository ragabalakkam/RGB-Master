<template>
  <client-page :on-created-actions="['business_types/index']">
    <h4 class="mb-3" v-text="createOrUpdate('organization')" />
    <form @submit.prevent="submit" @reset.prevent="$router.go(-1)">
      <div class="row">

        <!-- logo -->
        <b-form-group class="col-12" :label="$t('ال') + $t('logo')" >
          <b-img-input class="ht-220" v-model="form.image" :src="parseImg(form)" />
        </b-form-group>

        <!-- name -->
        <multi-lang-input :errors="errors" :name="$t('ال') + $t('name')" attr="name" class="col-12" no-resize v-model="form.name" required />

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
        <b-textarea class="col-12 mb-3" rows="3" v-model="form.notes" :errors="errors" :label="$t('ال') + $t('notes')" />
        
        <div class="col-12 d-flex align-items-center flex-gap-2">
          <b-button type="submit" variant="primary" v-text="$t('create')" />
          <b-button type="reset" v-text="$t('cancel')" class="py-1" />
        </div>
      </div>
    </form>
  </client-page>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientPage from '../../../../common/masters/Client/ClientPage.vue';
import PhoneInput from '../../../../common/components/Inputs/PhoneInput.vue';
import MultiLangInput from '../../../../common/components/Inputs/MultiLangInput.vue';
import AddressInput from '../../../../common/components/Inputs/AddressInput.vue';
import SelectBusinessType from '../../../Master/components/SelectBusinessType.vue';
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
      apps: 'client/apps',
      business_types: 'business_types/business_types',
    }),
    _id() {
      return this.id || this.$route.params.org_id;
    },
    _action() {
      return this.action || this.$route.params.action || 'create';
    },
  },
  created: async function() {
    this.loading = true;
    await this.$store.dispatch('client/findOrganization', this._id)
      .then(data => this.form = data)
      .catch(errors => this.errors = errors);
    this.loading = false;
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store.dispatch(`client/${this._id ? 'update' : 'create'}Organization`, this.form)
        .then(org => this.$router.push({ name: 'organizations.show', params: { org_id: org.id } }))
        .catch(errors => this.errors = errors);
      this.loading = false;
    },
  },
  components: {
    ClientPage,
    PhoneInput,
    MultiLangInput,
    AddressInput,
    SelectBusinessType,
  },
};
</script>