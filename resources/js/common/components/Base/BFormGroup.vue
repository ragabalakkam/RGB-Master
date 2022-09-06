<template>
  <fieldset v-bind="$attrs" class="form-group" @click="e => $emit('click', e)">
    <label v-if="label" :for="labelFor" :class="labelClass || 'mb-0 fs-3 text-secondary-9'">
      <b-i v-if="icon" :icon="icon" />
      <span v-html="`${is_required ? `&#10045; ` : ''}${ucfirst ? ucFirst(label) : label}`"/>
    </label>
    <slot />
  </fieldset>
</template>

<script>
export default {
  name: "b-form-group",
  props: {
    label       : { default: null },
    labelFor    : { default: null },
    labelClass  : { default: null },
    icon        : { default: null },
    ucfirst     : { default: true },
    required    : { default: false },
  },
  computed: {
    is_required() {
      return this.required || this.required === '';
    },
  },
};
</script>

<style lang="scss" scoped>
fieldset {
  label {
    font-family: 'DroidKufi', 'Cairo';
  }
}
</style>