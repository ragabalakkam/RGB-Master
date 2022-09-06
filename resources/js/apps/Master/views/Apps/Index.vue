<template>
  <widget
    v-model="loading"
    :models="apps"
    :title="$t('ال') + $t('apps')"
    :link="{ name: 'apps.create', params: { action: 'create' } }"
    :on-created-actions="['apps/index']"
    :not-yet-attr="$t('apps')"
    :attr="$t('app')"
    permission="apps"
  >
    <flex-table
      class="mb-4"
      :data="apps"
      :head="{
        image: $t('ال') + $t('image'),
        name: $t('ال') + $t('name'),
        description: $t('ال') + $t('description'),
        actions: $t('actions'),
      }"
      :casts="{
        name: x => parseName(x),
        description: x => abbr(parseName(x), 250),
      }"
      :on-item-clicked="({ id }) => $router.push({ name: 'apps.show', params: { id } })"
      :hidden-xs="['description']"
      :classes="{ description: 'flex-2' }"
      module="apps"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
const FlexTable = () => import('../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  names: "apps.index",
  data() {
    return {
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      apps: 'apps/apps',
    }),
  },
  components: {
    Widget,
    FlexTable,
  },
};
</script>
