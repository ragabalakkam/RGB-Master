<template>
  <create-page
    @submit="x => atSubmit !== false ? atSubmit(x) : $router.push({ name: 'roles.index' })"
    @reset="x => atReset !== false ? atReset(x) : $router.push({ name: 'roles.index' })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :on-created-actions="['permissions/index']"
    :title="createOrUpdate('role', _action)"
    :action="_action"
    :id="_id"
    module="permissions"
    permission="roles"
    v-model="form"
  >
    <!-- name -->
    <name-input v-model="form.name" :errors="errors" class="col-12" />

    <!-- priority -->
    <b-input
      class="col-12"
      type="number"
      min="0"
      step="1"
      :max="highest_priority - 1"
      v-model="form.priority"
      :label="$t('priority')"
    />

    <!-- permissions -->
    <b-form-group class="col-12" :label="ucFirst($t('ال') + $t('permissions'))">
      <div
        class="border-left border-2 py-2 row mx-0"
        :class="`border-${errors.permission_ids ? 'danger' : 'light'}`"
      >
        <permissions-group-checkbox
          class="mb-4 col-sm-6 col-md-4 col-xl-3"
          v-for="group in permissions_groups"
          :key="group.id"
          :group="group"
          v-model="form.permission_ids"
        />
      </div>
      <b-error :field="errors.permission_ids" attr="permissions" />
    </b-form-group>
  </create-page>
</template>

<script>
import { mapGetters } from "vuex";
const CreatePage = () => import("../../../../common/masters/ControlPanel/pages/CreatePage");
import NameInput from '../../../../common/components/Inputs/NameInput.vue';
import PermissionsGroupCheckbox from "./components/PermissionsGroupCheckbox.vue";
export default {
  name: "roles.create",
  props: {
    atSubmit    : { default: false }, 
    atReset     : { default: false },
    startingForm: { default: null },
    action      : { default: null },
    id          : { default: null },
  },
  data() {
    return {
      form: {
        name: null,
        priority: 0,
        permission_ids: [],
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      roles: 'permissions/roles',
      permissions_groups: "permissions/permissions_groups",
      highest_priority: 'permissions/highest_priority'
    }),
    _id() {
      return this.id || this.$route.params.id;
    },
    _action() {
      return this.action || this.$route.params.action || "create";
    },
  },
  mounted: function () {
    if (this.startingForm) this.form = this.startingForm; 
  },
  components: {
    CreatePage,
    NameInput,
    PermissionsGroupCheckbox,
  },
};
</script>
