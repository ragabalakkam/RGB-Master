<template>
  <create-page
    @submit="$router.push({ name: route })"
    @reset="$router.push({ name: route })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :on-created-actions="['locations/fetch', 'locations/getCities']"
    :title="createOrUpdate('district')"
    module="locations"
    module-postfix="District"
    v-model="form"
    :mounted-postprocessing="postProcessing"
  >
    <!-- country -->
    <b-select
      class="col-12"
      v-model="country_id"
      :label="$t('ال') + $t('country')"
      :null-option-attr="$t('country')"
    >
      <option v-for="country in countries" :key="country.id" :value="country.id" v-text="parseName(country.name)" />
    </b-select>

    <!-- state -->
    <b-select
      class="col-12"
      v-model="state_id"
      :label="$t('ال') + $t('state')"
      :null-option-attr="$t('state')"
    >
      <option v-for="state in Object.values(states).filter(s => s.country_id == country_id)" :key="state.id" :value="state.id" v-text="parseName(state.name)" />
    </b-select>

    <!-- city_id -->
    <b-select
      :class="{ 'is-invalid': errors.city_id }"
      class="col-12"
      attr="city"
      v-model="form.city_id"
      :label="$t('ال') + $t('city')"
      :null-option-attr="$t('city')"
    >
      <option v-for="city in Object.values(cities).filter(c => c.state_id == state_id)" :key="city.id" :value="city.id" v-text="parseName(city.name)" />
    </b-select>

    <!-- name -->
    <name-input v-model="form.name" :errors="errors" class="col-12" />
  </create-page>
</template>

<script>
import { mapGetters } from "vuex";
const CreatePage = () => import("../../../../../common/masters/ControlPanel/pages/CreatePage");
const NameInput = () => import("../../../../../common/components/Inputs/NameInput");
export default {
  name: "create-district",
  data() {
    return {
      country_id: null,
      state_id: null,
      form: {
        name: null,
        city_id: null,
      },
      route: 'locations.districts.index',
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
      cities: "locations/cities",
      districts: "locations/districts",
    }),
  },
  methods: {
    postProcessing: function () {
      if (this.form.city_id) {
        setTimeout(() => {
          let city = this.cities[this.form.city_id];
          
          if (city) {
            this.state_id = city.state_id

            let state = this.states[city.state_id];
            if (state) this.country_id = state.country_id;
          }
        }, 5);
      }
    },
  },
  components: {
    CreatePage,
    NameInput,
  },
};
</script>