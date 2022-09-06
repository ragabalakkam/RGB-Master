<template>
  <input
    class="form-control"
    v-bind="$attrs"
    v-model="input"
    :type="type"
    :disabled="disabled"
    :value="val"
    :min="min"
    :max="max"
    :step="step"
    @blur="$emit('blur')"
    @focus="$emit('focus')"
    @click="$emit('click')"
    @change="$emit('change')"
    @keyup="$emit('keyup')"
    @keydown="$emit('keydown')"
    @keypress="$emit('keypress')"
  />
</template>

<script>
export default {
  name: "b-input",
  props: {
    value     : { required: true },
    type      : { default: "text" },
    disabled  : { default: false },
    val       : { default: null },
    min       : { default: 0 },
    max       : { default: Number.MAX_SAFE_INTEGER },
    step      : { default: 'any' },
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
  },
  watch : {
    input: function (val) {
      if (this.type == 'number' && val > this.max) {
        this.input = this.max;
      }
    },
  },
};
</script>