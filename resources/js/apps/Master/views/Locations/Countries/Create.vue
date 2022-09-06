<template>
  <create-page
    @submit="$router.push({ name: route })"
    @reset="$router.push({ name: route })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :title="createOrUpdate('country')"
    module="locations"
    module-postfix="Country"
    v-model="form"
  >
    <!-- name -->
    <name-input v-model="form.name" :errors="errors" class="col-12" />
  </create-page>
</template>

<script>
const CreatePage = () => import("../../../../../common/masters/ControlPanel/pages/CreatePage");
const NameInput = () => import("../../../../../common/components/Inputs/NameInput");
export default {
  name: "create-country",
  data() {
    return {
      form: {
        name: null,
      },
      route: 'locations.countries.index',
      errors: {},
      loading: false,
    };
  },
  components: {
    CreatePage,
    NameInput,
  },
};
</script>