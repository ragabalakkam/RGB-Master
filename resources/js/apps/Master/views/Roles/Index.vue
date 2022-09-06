<template>
  <widget
    :title="$t('ال') + $t('roles')"
    :link="{ name: 'roles.create', params: { action: 'create' } }"
    :on-created-actions="['permissions/index']"
    :models="roles"
    v-model="loading"
    :not-yet-attr="$t('roles')"
    :attr="$t('role')"
    permission="roles"
  >
    <flex-table
      :data="withoutTrashed(roles).filter(r => r.priority <= highest_priority).sort((a, b) => b.priority - a.priority)"
      :head="{
        id      : $t('id'),
        name    : $t('ال') + $t('name'),
        actions : $t('actions'),
      }"
      :casts="{
        name    : x => parseName(x),
      }"
      module="permissions"
      route="roles"
      permission="roles"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
import FlexTable from '../../../../common/masters/ControlPanel/components/FlexTable.vue';
export default {
  names: "roles.index",
  data() {
    return {
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      roles: "permissions/roles",
      highest_priority: 'permissions/highest_priority',
    }),
    canUpdate() {
      return this.can('roles.update');
    },
    canDelete() {
      return this.can('roles.destroy');
    },
  },
  components: {
    Widget,
    FlexTable
  },
};
</script>