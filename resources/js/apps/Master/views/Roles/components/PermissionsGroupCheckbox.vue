<template>
  <div class="text-secondary-7">
    <div
      class="d-block d-flex align-items-center mb-1 text-hover-primary c-ptr"
      :class="`text-${allSelected ? 'primary' : 'secondary'}`"
      @click="allSelected ? deselectAll() : selectAll()"
    >
      <span class="btn btn-light btn-sm font-md py-0 bg-info-1 rounded" v-text="ucFirst(name)" />
    </div>
    <b-checkbox
      v-for="permission in group.permissions"
      :key="permission.id"
      class="d-flex align-items-center flex-gap-2 mb-0"
      :class="{ 'text-info': can(permission.id) }"
      :val="permission.id"
      v-model="permissions"
    >
      {{ t(permission.name) }}
    </b-checkbox>
  </div>
</template>

<script>
export default {
  name: "permissions-group-checkbox",
  props: ["value", "group"],
  computed: {
    permissions: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
    allSelected() {
      let allSelected = true;
      Object.values(this.group.permissions).forEach((permission) => {
        if (!this.permissions.includes(permission.id)) allSelected = false;
      });
      return allSelected;
    },
    name() {
      let name = this.group.name, t = this.$t;
      if (name.includes('.')) name = name.split('.'); 
      return typeof name == 'string' ? t(this.group.name) : t('X', { 0: t(name[0]), 1: t(name[1]) });
    },
  },
  methods: {
    selectAll: function () {
      Object.values(this.group.permissions).forEach((permission) => {
        if (!this.permissions.includes(permission.id))
          this.permissions.push(permission.id);
      });
    },
    deselectAll: function () {
      let ids = Object.values(this.group.permissions).map(
        (permission) => permission.id
      );
      Object.values(this.group.permissions).forEach((permission) => {
        this.permissions = this.permissions.filter((id) => !ids.includes(id));
      });
    },
    t: function(permission_name) {
      permission_name = permission_name.split(' ', 2);
      const name = permission_name.pop();
      const action = permission_name.pop();
      return `${this.$t(action)} ${this.$t(name)}`;
    },
  },
};
</script>