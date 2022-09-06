<template>
  <widget>
    <p v-if="employee" class="font-xl" v-text="employee.name"></p>
    <hr />
    <template v-if="!loading && employee">
      <div>
        <p>id: {{ employee.id }}</p>
      </div>
      <hr />
      <div class="d-flex">
        <router-link v-if="can('employees.update')" :to="{ name: 'employees.create', params: { action: 'update', id: employee.id }}" class="btn btn-primary mr-2">
          <b-i icon="pen" class="mr-2" />
          <span v-t="'edit'" />
        </router-link>
        <b-button v-if="can('employees.destroy')" variant="danger" @click.stop="confirmDelete(employee, 'employees/delete')">
          <b-i icon="trash" class="mr-2" />
          <span v-t="'delete'" />
        </b-button>
      </div>
    </template>
    <clip-loader v-else />
  </widget>
</template>

<script>
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
export default {
  names: "employees.show",
  data() {
    return {
      employee: null,
      loading: true,
    };
  },
  mounted() {
    this.$store
      .dispatch("employees/find", this.$route.params.id)
      .then((employee) => {
        this.employee = employee;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "employees.index" }));
  },
  components: {
    Widget,
  },
};
</script>

<style>
</style>
