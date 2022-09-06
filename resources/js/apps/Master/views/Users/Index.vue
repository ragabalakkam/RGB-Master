<template>
  <widget
    v-model="loading"
    :models="employees"
    :title="$t('ال') + $t('users')"
    :on-created-actions="['employees/index']"
    :link="{ name: 'employees.create', params: { action: 'create' } }"
    :not-yet-attr="$t('users')"
    :attr="$t('user')"
    permission="employees"
  >
    <!-- employees -->
    <flex-table
      :data="withoutTrashed(employees).filter(emp => emp.priority <= user.priority).sort((a, b) => b.priority - a.priority)"
      :head="{
        id            : $t('id'),
        name          : $t('ال') + $t('name'),
        created_at    : $t('X', { 0: $t('date'), 1: $t('create') }),
        actions       : $t('actions'),
      }"
      :casts="{
        created_at    : castTime,
      }"
      module="employees"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
import FlexTable from '../../../../common/masters/ControlPanel/components/FlexTable.vue';
export default {
  names: "employees.index",
  data() {
    return {
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      employees: 'employees/employees',
    }),
  },
  components: {
    Widget,
    FlexTable,
  },
};
</script>
