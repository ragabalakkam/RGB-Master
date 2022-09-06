<template>
  <b-form-group
    class="b-select"
    :label-for="_id"
    :label="showLabel && (label || attr || name) ? ucFirst(label || $t(attr) || $t(name) || '') : null"
    :title="errors && errors[name] ? errors[name] : label"
  >
    <p
      class="form-control c-text"
      :class="[
        { 'text-secondary-6': !select },
        { 'custom-select' : hasArrows },
        { 'bg-light c-not-allowed' : disabled },
        { 'is-invalid' : showError && errors && name && errors[name] },
        inputClass
      ]"
      :style="inputStyle"
      @click="openForm"
    >
      <span style="white-space: nowrap" v-text="_optionsByValues[select] ? _optionsByValues[select].plain || _optionsByValues[select].label : '--'" />
    </p>

    <b-error
      :field="errors[name]"
      :attr="attr || name"
      v-if="errors && showError && name"
    />
  </b-form-group>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "b-select",
  props: {
    value             : { required: true },
    name              : { required: false },
    label             : { default: null },
    errors            : { required: false },
    showError         : { default: true },
    options           : { required: false, type: Array },
    id                : { required: false },
    attr              : { required: false },
    showLabel         : { default: true },
    inputClass        : { default: null },
    inputStyle        : { default: null },
    nullOptionAttr    : { default: null },
    nullOptionDisabled: { default: true },
    data              : { default: null },
    val               : { default: "id" },
    txt               : { default: "name" },
    lbl               : { default: "name" },
    castNullOption    : { default: null },
    castLabel         : { default: null },
    castText          : { default: null },
    castValue         : { default: null },
    arrows            : { default: '' },
    disabled          : { default: false },
  },
  data() {
    return {
      coords: { top: '50%', left: '50%' },
    };
  },
  computed: {
    ...mapGetters({
      select_inputs: 'select',
    }),
    select_input() {
      return this.select_inputs[this._id];
    },
    select: {
      set(value) {
        this.$emit("input", value);
        this.$emit("change", value);
        this.$store.commit('SET_INPUT', { name: 'select', id: this._id, value: { ...this.select_input, value, coords: this.coords, hasArrows: this.hasArrows }});
      },
      get() {
        return this.value;
      },
    },
    focused: {
      set(focused) {
        this.$store.commit('SET_INPUT', { name: 'select', id: this._id, value: {
          ...this.select_input,
          coords: this.coords,
          id: this._id,
          focused,
          hasArrows: this.hasArrows,
          disabled: this.disabled,
          options: this._options,
          optionsByValues: this._optionsByValues,
        }})
      },
      get() {
        return this.select_input && this.select_input.focused;
      },
    },
    _id() {
      return this.id || `s-${Math.ceil(Math.random() * 1000000000)}`;
    },
    hasArrows() {
      return this.arrows || this.arrows === '';
    },
    _options() {
      let options = [];
      if (this.nullOptionAttr)
        options.push({
          label: this.castNullOption ? this.castNullOption(this.nullOptionAttr) : `-- ${this.$t("selectX", { attr: this.nullOptionAttr })} --`,
          value: null,
          disabled: this.nullOptionDisabled,
        });
      Object.values(this.data || {}).forEach((d) => {
        let label = this.castText ? this.castText(d[this.txt], d) : d[this.txt];
        options.push({
          label,
          value: this.castValue ? this.castValue(d[this.val], d) : d[this.val],
          plain: this.castLabel ? this.castLabel(d[this.txt], d) : label,
        });
      });
      return options;
    },
    _optionsByValues() {
      let options = {};
      this._options.forEach(opt => options[opt.value] = opt);
      return options;
    },
  },
  methods: {
    selectResult: function (value) {
      this.select = value;
      this.focused = false;
      this.hovered_at = null;
    },
    openForm: function (e) {
      if (this.disabled) return;
      let el = e.target;
      if (!el.classList.contains('form-control')) el = el.parentElement;
      this.coords = el.getBoundingClientRect();
      this.focused = true;
    },
  },
  watch: {
    select: {
      handler: function (select) {
        this.selectResult(select);
        this.$emit('change', select);
      },
      immediate: true,
    },
    "select_input.value":function (select_value) {
      this.select = select_value;
    },
  },
};
</script>