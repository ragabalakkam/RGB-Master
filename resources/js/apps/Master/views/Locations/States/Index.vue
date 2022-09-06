<template>
  <widget
    v-model="loading"
    :title="`${$t('ال') + $t('states')} / ${$t('ال') + $t('governorates')}`"
    :link="{ name: route, params: { action: 'create' }}"
    :on-created-actions="['locations/getCountries', 'locations/getStates']"
    :models="states"
    :not-yet-attr="$t('states')"
    :attr="$t('state')"
    permission="locations.states"
  >
    <flex-table
      :data="states"
      :head="{
        id          : $t('id'),
        name        : $t('ال') + $t('name'),
        country_id  : $t('ال') + $t('country'),
        actions     : $t('actions'),
      }"
      :casts="{
        name        : x => parseName(x),
        country_id  : id => parseName(countries[id].name),
      }"
      :on-item-clicked="can('locations.states.show') ? ({ id }) => $router.push({ name: route, params: { action: 'update', id }}) : null"
      module="locations"
      delete-postfix="State"
      permission="locations.states"
    />
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
const Widget = () => import("../../../../../common/masters/ControlPanel/components/Widget.vue");
const FlexTable = () => import('../../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  name: 'states.index',
  data() {
    return {
      loading: true,
      route: 'locations.states.create',
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
    }),
  },
  components: {
    Widget,
    FlexTable,
  },
}
</script>