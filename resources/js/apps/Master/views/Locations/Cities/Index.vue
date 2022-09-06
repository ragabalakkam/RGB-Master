<template>
  <widget
    :title="$t('ال') + $t('cities')"
    :link="{ name: route, params: { action: 'create' } }"
    v-model="loading"
    :on-created-actions="['locations/fetch', 'locations/getCities']"
    :models="cities"
    :not-yet-attr="$t('cities')"
    :attr="$t('city')"
    permission="locations.cities"
  >
    <flex-table
      :data="cities"
      :head="{
        id          : $t('id'),
        name        : $t('ال') + $t('name'),
        state_id    : $t('ال') + $t('state'),
        country_id  : $t('ال') + $t('country'),
        actions     : $t('actions'),
      }"
      :casts="{
        name        : x => parseName(x),
        state_id    : id => parseName(states[id].name),
        country_id  : (x, c) => parseName(countries[states[c.state_id].country_id].name),
      }"
      :on-item-clicked="can(route) ? ({ id }) => $router.push({ name: route, params: { action: 'update', id }}) : null"
      module="locations"
      delete-postfix="City"
      permission="locations.cities"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../../common/masters/ControlPanel/components/Widget");
const FlexTable = () => import('../../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  name: "cities.index",
  data() {
    return {
      loading: true,
      route: 'locations.cities.create',
    };
  },
  computed: {
    ...mapGetters({
      countries: "locations/countries",
      states: "locations/states",
      cities: "locations/cities",
    }),
  },
  components: {
    Widget,
    FlexTable,
  },
};
</script>