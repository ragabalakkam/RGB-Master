<template>
  <create-page
    @submit="$router.push({ name: route })"
    @reset="$router.push({ name: route })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :on-created-actions="['locations/fetch']"
    :title="createOrUpdate('city')"
    module="locations"
    module-postfix="City"
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
      :class="{ 'is-invalid': errors.state_id }"
      class="col-12"
      attr="state"
      v-model="form.state_id"
      :label="$t('ال') + $t('state')"
      :null-option-attr="$t('state')"
    >
      <option v-for="state in Object.values(states).filter(s => s.country_id == country_id)" :key="state.id" :value="state.id" v-text="parseName(state.name)" />
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
  name: "create-city",
  data() {
    return {
      country_id: null,
      form: {
        name: null,
        state_id: null,
      },
      route: 'locations.cities.index',
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
      cities: "locations/cities",
    }),
  },
  methods: {
    postProcessing: function () {
      if (this.form.state_id) {
        setTimeout(() => {
          let state = this.states[this.form.state_id];
          if (state) this.country_id = state.country_id;
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