<template>
  <widget
    :title="$t('ال') + $t('districts')"
    :link="{ name: route, params: { action: 'create' } }"
    v-model="loading"
    :on-created-actions="['locations/fetch', 'locations/getCities', 'locations/getDistricts']"
    :models="districts"
    :not-yet-attr="$t('districts')"
    :attr="$t('district')"
    permission="locations.districts"
  >
    <flex-table
      :data="districts"
      :head="{
        id          : $t('id'),
        name        : $t('ال') + $t('name'),
        city_id     : $t('ال') + $t('city'),
        state_id    : $t('ال') + $t('state'),
        country_id  : $t('ال') + $t('country'),
        actions     : $t('actions'),
      }"
      :casts="{
        name        : x => parseName(x),
        city_id     : id => parseName(cities[id].name),
        state_id    : (x, d) => parseName(states[cities[d.city_id].state_id].name),
        country_id  : (x, d) => parseName(countries[states[cities[d.city_id].state_id].country_id].name),
      }"
      :on-item-clicked="can(route) ? ({ id }) => $router.push({ name: route, params: { action: 'update', id }}) : null"
      module="locations"
      delete-postfix="District"
      permission="locations.districts"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../../common/masters/ControlPanel/components/Widget");
const FlexTable = () => import('../../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  name: "districts.index",
  data() {
    return {
      loading: true,
      route: 'locations.districts.create',
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
  components: {
    Widget,
    FlexTable,
  },
};
</script>