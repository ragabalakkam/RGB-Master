<template>
  <b-form-group
    class="b-input"
    :label-for="_id"
    :label="showLabel && (label || attr || name) ? label || $t(attr) || $t(name) || '' : null"
    @click="$emit('click')"
    :class="formClass"
    :title="errors && errors[name] ? $t(`validation.${errors[name]}`, { attr: '' }) : label"
    :ucfirst="ucfirst"
    :required="is_required"
  >
    <input
      :id="_id"
      v-bind="$attrs"
      :disabled="disabled"
      :type="type"
      v-model="input"
      :dir="inputDir"
      :val="val"
      :min="is_numeric ? _min : null"
      :max="is_numeric ? max : null"
      :step="step"
      ref="input"
      :class="[{ 'is-invalid': errors && name && errors[name] }, inputClass]"
      class="form-control"
      @keyup="e => $emit('keyup', e)"
      @keydown="e => $emit('keydown', e)"
      @keypress="e => $emit('keypress', e)"
      @change="onChange"
      @focus="e => $emit('focus', e)"
      @blur="e => $emit('blur', e)"
      :required="is_required"
      :onClick="type == 'number' ? 'this.value ? this.select() : null' : ''"
    />
    <b-error
      :field="errors[name]"
      :attr="attr || name"
      v-if="errors && showError && name"
      :attrs="attrs"
    />
  </b-form-group>
</template>

<script>
export default {
  name: "b-input",
  props: {
    value       : { required: true },
    type        : { default: "text" },
    name        : { required: false },
    label       : { default: null },
    errors      : { required: false },
    id          : { required: false },
    inputClass  : { default: null },
    formClass   : { default: null },
    attr        : { required: false },
    showError   : { default: true },
    showLabel   : { default: true },
    disabled    : { default: false },
    attrs       : { default: () => {} },
    val         : { default: null },
    min         : { default: Number.MIN_SAFE_INTEGER },
    max         : { default: Number.MAX_SAFE_INTEGER },
    step        : { default: 'any' },
    inputDir    : { default: null },
    ucfirst     : { default: true },
    required    : { default: false },
  },
  data() {
    return {
      timeout: null,
    };
  },
  computed: {
    input: {
      set(val) {
        if (this.is_numeric) val = val ? parseFloat(val) : val;
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    _id() {
      return this.id || `input-${this.name || ''}-${Math.ceil(Math.random() * 1000000000)}`;
    },
    _min() {
      return parseFloat(this.min) || 0;
    },
    is_numeric() {
      return ['number', 'range'].includes(this.type);
    },
    is_required() {
      return this.required !== false;
    },
  },
  methods: {
    focus: function () {
      document.getElementById(this._id).focus();
    },
    onChange: function (e) {
      this.$emit('change', e);
      this.handleChange(this.input, e);
    },
    handleChange: function (val, e) {
      switch(this.type)
      {
        case 'file':
        case 'image':
          let file = e.target.files[0];
          if (file) this.$emit('change-file', file);
          break;
        case 'number':
          if (this.timeout) clearTimeout(this.timeout);
          this.timeout = setTimeout(() => { if (this.type == 'number' && this._min > val) this.input = this._min }, 400)
          break;
      }
    },
  },
  watch: {
    input: 'handleChange',
  },
};
</script>