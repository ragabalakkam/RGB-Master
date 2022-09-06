<template>
  <b-form-group
    class="b-select"
    :label-for="_id"
    :label="showLabel && (label || attr || name) ? ucFirst(label || $t(attr) || $t(name) || '') : null"
    :title="errors && errors[name] ? $t(`validation.${errors[name]}`, { attr: '' }) : label"
  >
    <div class="position-relative h-100">
      <select
        :id="_id"
        v-bind="$attrs"
        v-model="select"
        class="form-control custom-select no-shadow"
        :class="[{'is-invalid': errors && errors[name] }, inputClass]"
        :style="inputStyle"
        @change="$emit('change')"
        @keyup="e => $emit('keyup', e)"
        @keydown="e => $emit('keydown', e)"
        @keypress="e => $emit('keypress', e)"
        @focus="e => $emit('focus', e)"
        @blur="e => $emit('blur', e)"
      >
        <template v-if="nullOption">
          <option
            v-text="nullOption"
            :value="null"
            :disabled="nullOptionDisabled"
          />
        </template>

        <template v-if="data">
          <option
            v-for="(element, i) in data"
            :key="i"
            :value="castValue(element[val], element)"
            v-text="castText(element[txt], element)"
          />
        </template>

        <template v-else-if="options && options.length">
          <option
            v-for="option in options"
            :key="option.value"
            :value="castValue(option)"
            v-text="castText(option)"
          />
        </template>

        <slot v-else />
      </select>
    </div>

    <b-error :field="errors[name]" :attr="attr || name" v-if="errors && showError && name" />
  </b-form-group>
</template>

<script>
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
    val               : { default: 'id' },
    txt               : { default: 'name' },
    castNullOption    : { default: null },
    castText          : { default: () => (x) => x },
    castValue         : { default: () => (x) => x },
  },
  data() {
    return {
      search: null,
    };
  },
  computed: {
    select: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    nullOption() {
      const attr = this.nullOptionAttr;
      return attr ? this.castNullOption ? this.castNullOption(attr) : `-- ${this.$t('selectX', { attr })} --`  : null;
    },
    // _options() {
    //   let options = [];

    //   if (this.nullOptionAttr)
    //     options.push({ label: `-- ${this.$t('selectX', { attr: this.nullOptionAttr })} --`, value: null });
      
    //   let data = typeof this.data == 'object' ? Object.values(this.data || {}) : this.data;
    //   data.forEach(d => options.push({ label: this.castText(d[this.txt], d), value: this.castValue(d[this.val], d)}));

    //   return options;
    // },
    filteredOptions() {
      const search = this.lower(this.search);
      return this._options.filter(op =>
        this.lower(op.text).includes(search) || this.lower(op.value).includes(search)
      );
    },
    _id() {
      return this.id || `select-${this.name}-${Math.ceil(Math.random() * 1000000000)}`;
    },
  },
  methods: {
    lower: function (val) {
      if (typeof val != 'string') val = val.toString();
      return val.toLowerCase();
    },
  },
};
</script>

<style lang="scss">
.search-input {
  width: calc(100% - var(--bs-spacer-3) - var(--bs-spacer-2));
  height: 96%;
}
</style>