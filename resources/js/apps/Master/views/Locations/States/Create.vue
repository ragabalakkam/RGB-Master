<template>
  <create-page
    @submit="$router.push({ name: route })"
    @reset="$router.push({ name: route })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :on-created-actions="['locations/fetch']"
    :title="createOrUpdate('state')"
    module="locations"
    module-postfix="State"
    v-model="form"
  >
    <!-- country_id -->
    <b-select
      class="col-12 no-shadow"
      :label="ucFirst($t('ال') + $t('country'))"
      name="country"
      :null-option-attr="$t('country')"
      :errors="errors"
      v-model="form.country_id"
    >
      <option v-for="country in countries" :key="country.id" :value="country.id" v-text="parseName(country.name)" />
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
  name: "create-state",
  data() {
    return {
      form: {
        country_id: null,
        name: null,
      },
      route: 'locations.states.index',
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
    }),
    id() {
      return this.$route.params.id;
    },
    action() {
      return this.$route.params.action;
    },
    state() {
      return this.action == "update" ? this.obj_clone(this.states[this.id] || {}) : null;
    },
  },
  components: {
    CreatePage,
    NameInput,
  },
};
</script>

<style></style>
