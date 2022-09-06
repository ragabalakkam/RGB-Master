<template>
  <multi-lang-input
    :name="label || $t('ال') + $t('name')"
    :errors="errors"
    v-model="input"
    :show-labels="showLabels"
    :gap="gap"
    :flex-column="flexColumn"
    no-resize
  />
</template>

<script>
import MultiLangInput from './MultiLangInput.vue';
export default {
  components: { MultiLangInput },
  name: "name-input",
  props: {
    value     : { required: true },
    label     : { default: null },
    errors    : { default: () => {} },
    showLabels: { default: true },
    gap       : { default: null },
    flexColumn: { default: false },
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
};
</script>