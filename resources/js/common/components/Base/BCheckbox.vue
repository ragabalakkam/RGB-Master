<template>
  <b-form-group :label="label">
    <div class="d-flex align-items-center flex-gap-2">
      <input
        v-model="input"
        v-bind="$attrs"
        type="checkbox"
        :value="val"
        :id="`checkbox-${_id}`"
        :disabled="disabled"
        :class="`${inputClass || ''} ${cls}`"
      />
      <label :for="`checkbox-${_id}`" class="c-ptr flex-1 mb-0" :class="cls">
        <slot />
      </label>
    </div>
  </b-form-group>
</template>

<script>
export default {
  name: "b-checkbox",
  props: {
    value       : { required: true },
    label       : { default: null },
    val         : { default: null },
    id          : { default: null },
    disabled    : { default: false },
    inputClass  : { default: null },
  },
  computed: {
    input: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    _id() {
      return this.id || Math.ceil(Math.random() * 100000);
    },
    cls() {
      let x = this.disabled;
      return `c-${x ? 'not-allowed text-secondary' : 'ptr'}`;
    },
  },
};
</script>