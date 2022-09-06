<template>
  <widget>
    <p v-if="role" class="font-xl" v-text="parseName(role.name)"></p>
    <hr />
    <template v-if="!loading && role">
      <div>
        <p class="mb-3">{{ $t('id') }} : {{ role.id }}</p>
        <p class="mb-3">{{ $t('name') }} : {{ parseName(role.name) }}</p>
        <div class="font-md text-secondary">
          <p
            class="bg-light d-inline-block rounded-edges px-2 mr-2 mb-2"
            v-for="id in role.permissions_ids"
            :key="id"
            v-text="t(permissions[id].name)"
          />
        </div>
      </div>
      <hr />
      <div class="d-flex">
        <router-link v-if="can('roles.update')" :to="{name: 'roles.create', params: { action: 'update', id: role.id }}" class="btn btn-primary mr-2">
          <b-i icon="pen" class="mr-2" />
          <span v-t="'edit'"></span>
        </router-link>
        <b-button v-if="can('roles.destroy')" variant="danger" @click.stop="confirmDelete(role, 'permissions/delete')">
          <b-i icon="trash" class="mr-2" />
          <span v-t="'delete'"></span>
        </b-button>
      </div>
    </template>
    <clip-loader v-else />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
export default {
  names: "roles.show",
  data() {
    return {
      role: null,
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      permissions: "permissions/permissions",
    }),
  },
  mounted() {
    this.$store
      .dispatch("permissions/find", this.$route.params.id)
      .then((role) => {
        this.role = role;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "roles.index" }));
  },
  methods: {    
    t: function(permission_name) {
      permission_name = permission_name.split(' ', 2);
      const name = permission_name.pop();
      const action = permission_name.pop();
      return ['show', 'create', 'update', 'delete'].includes(action)
        ? this.$t(`${action}X`, { attr: this.$t(name)})
        : `${this.$t(action)} ${this.$t(name)}`;
    }
  },
  components: {
    Widget,
  },
};
</script>
