<template>
  <b-form-group :label="label || $t('address')">
    <div class="d-flex flex-column" :class="`flex-gap-${isXS ? 1 : 2}`">
      <div class="d-flex" :class="[{'flex-column' : isXS}, `flex-gap-${isXS ? 1 : 2}`]">
        <div class="flex-1 d-flex" :class="`flex-gap-${isXS ? 1 : 2}`">
          <!-- select country -->
          <b-select
            class="flex-1 mb-0"
            :data="countries"
            :cast-text="x => parseName(x)"
            :null-option-attr="$t('ال') + $t('country')"
            :null-option-disabled="false"
            v-model="address[0]"
            @change="updateStates"
            :disabled="disabled"
            :required="required"
          />

          <!-- select state -->
          <b-select
            class="flex-1 mb-0"
            :data="selectedStates"
            :cast-text="x => parseName(x)"
            :null-option-attr="$t('ال') + $t('state')"
            :null-option-disabled="false"
            v-model="address[1]"
            @change="updateCities"
            :disabled="disabled"
            :required="required"
          />
        </div>
        <div class="flex-1 d-flex" :class="`flex-gap-${isXS ? 1 : 2}`">
          <!-- select city -->
          <b-select
            class="flex-1 mb-0"
            :data="selectedCities"
            :cast-text="x => parseName(x)"
            :null-option-attr="$t('ال') + $t('city')"
            :null-option-disabled="false"
            v-model="address[2]"
            @change="updateDistricts"
            :disabled="disabled"
            :required="required"
          />

          <!-- select district -->
          <b-select
            class="flex-1 mb-0"
            :data="selectedDistricts"
            :cast-text="x => parseName(x)"
            :null-option-attr="$t('ال') + $t('district')"
            :null-option-disabled="false"
            v-model="address[3]"
            :disabled="disabled"
            :required="required"
          />
        </div>
      </div>
      <div class="d-flex" :class="[{'flex-column' : isXS}, `flex-gap-${isXS ? 1 : 2}`]">

        <!-- street name -->
        <b-form-group class="flex-1" :class="`mb-${isXS ? 0 : 1}`">
          <b-labeled-input
            :label-class="{'wd-100' : isXS}"
            v-model="address[4]"
            :label="$t('X', { 0: $t('name'), 1: $t('street') })"
            :disabled="disabled"
            :required="required"
          />
        </b-form-group>

        <!-- bulding number -->
        <b-form-group class="flex-1" :class="`mb-${isXS ? 0 : 1}`">
          <b-labeled-input
            :label-class="{'wd-100' : isXS}"
            v-model="address[5]"
            :label="$t('X', { 0: $t('number'), 1: $t('building') })"
            :disabled="disabled"
            :required="required"
          />
        </b-form-group>
        
        <!-- postal code -->
        <b-form-group class="flex-1">
          <b-labeled-input
            :label-class="{'wd-100' : isXS}"
            v-model="address[6]"
            :label="$t('postalCode')"
            :disabled="disabled"
            :required="required"
          />
        </b-form-group>
      </div>
    </div>
  </b-form-group>
</template>

<script>
import { mapGetters } from "vuex";
import BSelect from './Select/BSelect.vue';
export default {
  components: { BSelect },
  name: "address-input",
  props: {
    value     : { required: true },
    forcedXs  : { default: false },
    label     : { default: null },
    required  : { default: false },
    disabled  : { default: false },
  },
  data() {
    return {
      loading : false,
    };
  },
  computed: {
    ...mapGetters({
      countries     : "locations/countries",
      states        : "locations/states",
      cities        : "locations/cities",
      districts     : "locations/districts",
    }),
    selectedStates() {
      return Object.values(this.states).filter(s => s.country_id === this.address[0]);
    },
    selectedCities() {
      return Object.values(this.cities).filter(c => c.state_id == this.address[1]);
    },
    selectedDistricts() {
      return Object.values(this.districts).filter(c => c.city_id == this.address[2]);
    },
    isXS() {
      return this.wXS || this.forcedXs;
    },
    address: {
      get() {
        return this.value || [];
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  created: async function () {
    this.loading = true;
    await this.$store.dispatch("locations/fetch");
    if (this.value && this.value[2]) {
      this.updateStates();
      this.updateCities();
      this.updateDistricts();
    } else this.address = [null, null, null, null, null, null, null];
    this.loading = false;
  },
  methods: {
    updateStates: function () {
      this.$store.dispatch('locations/fetchStates', this.address[0]);
      this.state_id = null;
      this.city_id = null;
    },
    updateCities: function () {
      this.city_id = null;
      this.district_id = null;
      this.$store.dispatch('locations/updateCities', this.address[1]);
    },
    updateDistricts: function () {
      this.district_id = null;
      this.$store.dispatch('locations/updateDistricts', this.address[2]);
    },
  },
  watch: {
    "address.0" : function () {
      this.address[1] = null;
    },
    "address.1" : function () {
      this.address[2] = null;
    },
    "address.2" : function () {
      this.address[3] = null;
    },
    "address.3" : function () {
      this.address[4] = null;
    },
  },
};
</script>
