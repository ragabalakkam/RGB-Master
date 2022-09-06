<template>
  <b-form-group class="b-textarea" :label="showLabel ? label : null" :required="required">
    <textarea
      class="form-control"
      @keydown.enter.prevent="e => {
        input = (input || '') + '\n';
        $emit('keydown', e);
      }"
      :rows="rows"
      v-model="input"
      v-bind="$attrs"
      :colmuns="colmuns"
      :id="textId"
      :class="textClass"
      :placeholder="placeholder"
      :spellcheck="spellcheck"
      :required="is_required"
    ></textarea>
  </b-form-group>
</template>

<script>
export default {
  name: "b-textarea",
  props: {
    rows: { default: 1 },
    colmuns: { default: null },
    value: { required: true },
    placeholder: { default: null },
    limit: { default: 200 },
    textId: { default: null },
    textClass: { default: null },
    spellcheck: { default: false },
    maxRows: { default: 10 },
    responsiveHeight: { default: false },
    label: { default: null },
    showLabel: { default: true },
    required: { default: false },
  },
  computed: {
    input: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
    is_required() {
      return this.required !== false;
    },
  },
};
</script>

<style lang="scss" scoped>
::-webkit-scrollbar {
  appearance: none;
}
</style>