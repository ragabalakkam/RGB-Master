<template>
  <div>
    <widget>
      <form @submit.prevent="updateNumbers" @reset.prevent="resetNumbers">
        <div class="row mb-3 mb-md-0">
          <!-- branches -->
          <b-input
            class="col-md-6"
            name="number_of_branches"
            :errors="errors"
            :label="$t('number_of_branches')"
            type="number"
            :step="1"
            v-model="number_of_branches"
            :disabled="!editingBranchesAndPointsOfSale"
          />
          <!-- points of sale -->
          <b-input
            class="col-md-6"
            name="number_of_points_of_sale"
            :errors="errors"
            :label="$t('number_of_points_of_sale')"
            type="number"
            :step="1"
            v-model="number_of_points_of_sale"
            :disabled="!editingBranchesAndPointsOfSale"
          />
        </div>

        <!--  -->
        <button
          type="button"
          v-if="!editingBranchesAndPointsOfSale"
          class="bg-none border-0 text-primary"
          @click="editingBranchesAndPointsOfSale = true"
          v-text="$t('editX', { attr: $t('xOfy', { x: $t('count'), y: `${$t('ال') + $t('branches')} / ${$t('thePointsOfSale')}` }) })"
        />

        <!--  -->
        <div v-else class="d-flex flex-gap-2">
          <b-button variant="primary" type="submit" v-t="'update'" :disabled="loading" />
          <b-button variant="light" type="reset" v-t="'cancel'" :disabled="loading" />
        </div>
      </form>
    </widget>

    <!-- modules -->
    <widget v-model="loading" class="manage-modules-page" :title="$t('X', { 0: $t('modules'), 1: $t('client') })">
      <form v-if="!loading" @submit.prevent="submit" @reset.prevent="reset">
        <b-input v-model="search" :placeholder="$t('searchByX', { x: $t('X', [$t('name'), $t('module')]) })" />

        <hr>

        <div class="row">
          <div class="col-md-4" v-for="name in filtered_mods" :key="name">
            <b-on-off-input v-model="mods[name]" size="lg">
              <p v-text="ucFirst(name.startsWith('print_report.') ? $t('xOfy', { x: $t('reports'), y: $t(name)}) : $t(`master_modules.${name}`))" />
            </b-on-off-input>
            <hr class="my-2" />
          </div>
        </div>

        <div class="d-flex mt-4">
          <b-button variant="primary" type="submit" v-t="'update'" :disabled="disabled" />
          <b-button variant="reset" type="reset" v-t="'cancel'" :disabled="disabled" />
        </div>
      </form>
    </widget>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../masters/ControlPanel/components/Widget");
export default {
  name: "manage-modules-page",
  data() {
    return {
      search: null,
      mods: {},
      errors: {},
      loading: false,
      number_of_branches: null,
      number_of_points_of_sale: null,
      editingBranchesAndPointsOfSale: false,
    };
  },
  computed: {
    ...mapGetters({
      modules: "configurations/modules",
      app: "app",
      branches_count: "configurations/number_of_branches",
      points_of_sale_count: "configurations/number_of_points_of_sale",
    }),
    disabled() {
      let changed = false;
      Object.keys(this.mods).forEach((key) => { if (this.mods[key] !== this.modules[key]) changed = true });
      return !changed;
    },
    filtered_mods() {
      let t = this.$t,
        s = this.search;
      if (s) s = s.toLowerCase();
      return Object.keys(this.mods).filter(m => !s || (m.startsWith('print_report.') ? t('xOfy', { x: t('reports'), y: t(m)}) : t(`master_modules.${m}`)).toLowerCase().includes(s))
    },
  },
  mounted() {
    this.mods = { ...this.modules };
    this.resetNumbers();
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store.dispatch("configurations/update", { key: "modules", value: this.mods });
      this.loading = false;
    },
    reset: function () {
      this.mods = this.modules;
    },
    updateNumbers: async function () {
      this.loading = true;
      let valid = true;
      await this.$store
        .dispatch("configurations/update", {
          key: "number_of_branches",
          value: this.number_of_branches,
        })
        .catch((error) => {
          this.errors = { ...this.errors, ...error };
          valid = false;
        });
      await this.$store
        .dispatch("configurations/update", {
          key: "number_of_points_of_sale",
          value: this.number_of_points_of_sale,
        })
        .catch((error) => {
          this.errors = { ...this.errors, ...error };
          valid = false;
        });

      if (valid) this.resetNumbers();

      this.loading = false;
    },
    resetNumbers: function () {
      this.number_of_branches = this.branches_count;
      this.number_of_points_of_sale = this.points_of_sale_count;
      delete this.errors.number_of_branches;
      delete this.errors.number_of_points_of_sale;
      this.editingBranchesAndPointsOfSale = false;
    },
  },
  components: {
    Widget,
  },
};
</script>