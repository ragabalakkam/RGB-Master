<template>
  <div class="d-flex flex-wrap" v-if="obj_length(all_roles)">
    <!-- roles -->
    <span
      class="btn btn-outline-info btn-sm rounded-edges text-hover-white flex-gap-1 ht-35 w-max-content mr-2 p-2"
      v-for="(roleID, i) in input"
      :key="i"
      @click="remove(i)"
    >
      {{ all_roles[roleID] ? parseName(all_roles[roleID].name) : "" }}
      <span class="text-inherit pl-1">
        <b-i icon="times" class="fs-2" />
      </span>
    </span>

    <!-- add role button -->
    <b-button
      variant="info"
      class="ht-35 rounded-edges text-hover-white no-shadow mr-2 py-0"
      v-if="!addingRole && roles.length"
      @click="addingRole = true"
    >
      <b-i icon="plus" class="fs-2" />
      {{ $t("selectX", { attr: $t(('role') )}) }}
    </b-button>

    <!-- select new role -->
    <b-select
      v-else-if="roles.length"
      :data="roles"
      :cast-text="x => parseName(x)"
      class="ht-35 wd-90 border-info text-info py-0 wd-150"
      input-class="rounded-edges"
      :null-option-attr="$t('ال') + $t('role')"
      v-model="role"
    />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "roles-input",
  props: {
    value: { required: true },
    label: { default: null },
  },
  data() {
    return {
      role: null,
      addingRole: false,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      all_roles: 'permissions/roles',
    }),
    input: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
    roles() {
      return this.withoutTrashed(this.all_roles)
        .filter(r => !this.user || r.priority <= this.user.priority)
        .filter(r => !this.input.includes(r.id));
    },
  },
  created: function () {
    this.$store.dispatch("permissions/index");
  },
  methods: {
    add: function () {
      //
    },
    remove: function (index) {
      this.input.splice(index, 1);
    },
  },
  watch: {
    role: function (newVal) {
      if (newVal) this.input.push(newVal);
      this.addingRole = false;
      this.role = null;
    },
  },
};
</script>