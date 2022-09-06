<template>
  <div class="text-danger" v-if="field">
    <b-i icon="info-circle" />
    <span v-text="$t(`${t}.${field[0]}`, { attr: $t(attr), ...attrs })"></span>
  </div>
</template>

<script>
export default {
  name: "b-error",
  props: {
    field: { required: true },
    attr: { required: false },
    t: { required: false, default: "validation" },
    attrs: { default: () => {} },
  },
};
</script>