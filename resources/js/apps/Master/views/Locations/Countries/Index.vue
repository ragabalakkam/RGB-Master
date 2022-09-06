<template>
  <widget
    :title="$t('ال') + $t('countries')"
    :link="{ name: route, params: { action: 'create' }}"
    v-model="loading"
    :on-created-actions="['locations/fetch']"
    :models="countries"
    :not-yet-attr="$t('countries')"
    :attr="$t('country')"
    permission="locations.countries"
  >
    <flex-table
      :data="countries"
      :head="{
        id      : $t('id'),
        name    : $t('ال') + $t('name'),
        actions : $t('actions'),
      }"
      :casts="{
        name    : x => parseName(x)
      }"
      :on-item-clicked="can('locations.countries.show') ? ({ id }) => $router.push({ name: route, params: { action: 'update', id }}) : null"
      module="locations"
      delete-postfix="Country"
      permission="locations.countries"
    />
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
const Widget = () => import("../../../../../common/masters/ControlPanel/components/Widget");
const FlexTable = () => import('../../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  name: 'countries.index',
  data() {
    return {
      loading: true,
      route: 'locations.countries.create',
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
    }),
  },
  methods: {
    statesCount: function(county) {
      return Object.values(this.states).filter(state => state.country_id == county.id).length;
    },
  },
  components: {
    Widget,
    FlexTable,
  },
}
</script>