<template>
  <b-form-group :label="label">
    <div class="form-control p-0 d-flex" :class="[formClass, { 'border-primary': focused }]">
      <b-input
        type="number"
        step="any"
        class="flex-1 h-100 mb-0"
        :input-class="`border-0 h-100 ${inputClass}`"
        v-model="input_value"
        v-bind="$attrs"
        :min="0"
        @focus="focused = true"
        @blur="focused = false"
      />

      <b-select
        :showLabel="false"
        :errors="errors"
        name="unit"
        class="h-100"
        :input-class="[selectClass, 'border-0 h-100 no-shadow']"
        :input-style="selectStyle"
        :class="[selectClass, `bg-${variant}`, { 'border-left border-primary': focused }]"
        :null-option-attr="stateId && base_unit ? null : $t('unit')"
        v-model="unit_id"
        @focus="focused = true"
        @blur="focused = false"
        :arrows="arrows"
      >
        <option
          v-for="unit in units"
          :key="unit.id"
          :value="unit.id"
          v-text="parseName(all_units[unit.id].name)"
        />
      </b-select>
    </div>

    <div
      v-if="!isCreated"
      class="position-absolute position-top-left h-100 w-100 d-flex-center bg-white-5"
    >
      <clip-loader size="0.8rem" />
    </div>
  </b-form-group>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "unit-input",
  props: {
    value       : { required: true },
    stateId     : { default: null },
    errors      : { default: () => {} },
    label       : { default: null },
    variant     : { default: 'light' },
    inputClass  : { default: '' },
    selectClass : { default: '' },
    selectStyle : { default: '' },
    formClass   : { default: '' },
    arrows      : { dafault: true },
  },
  data() {
    return {
      unit_id: null,
      input_value: null,
      focused: false,
      isCreated: false,
    };
  },
  computed: {
    ...mapGetters({
      all_units: "units/units",
      base_units: "units/base_units",
      sorted_units: "units/sorted_units",
      states: "ingredients/states",
    }),
    units() {
      return this.sorted_units[this.stateId];
    },
    base_unit() {
      return this.all_units[this.base_units[this.stateId]] || null;
    },
    unit() {
      return this.all_units[this.unit_id];
    },
    input: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
  },
  created: async function () {
    await this.$store.dispatch("ingredients/fetchStates");
    await this.$store.dispatch("units/index");
    this.isCreated = true;
  },
  methods: {
    calcValue: function () {
      if (this.unit_id)
        this.input = this.input_value * this.unit.value_to_base_unit;
    },
    init: function () {
      if (!this.stateId || !this.base_unit) return;

      let unit_id = this.input == 1 ? this.base_unit.id : null;

      this.units.forEach((unit) => {
        if (this.input > unit.value) unit_id = unit.id;
        else return;
      });

      if (unit_id) {
        this.input_value = this.round(
          this.input / this.all_units[unit_id].value_to_base_unit
        );
        this.unit_id = unit_id;
      }
    },
  },
  watch: {
    unit_id: "calcValue",
    input_value: "calcValue",
    input: function () { this.$emit('change'); },
    stateId: function (state_id) {
      if (this.isCreated && state_id) this.init();
      if (state_id && this.base_units) this.unit_id = this.base_units[state_id];
    },
    isCreated: function (isCreated) {
      if (isCreated && this.stateId) this.init();
    },
  },
};
</script>