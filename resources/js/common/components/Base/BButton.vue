<template>
  <button
    class="btn"
    :type="type"
    v-bind="$attrs"
    :class="[`btn-${variant}`, size ? `btn-${size}` : null, { 'btn-block': block === '' }, `${shadow === '' ? '' : 'no-'}shadow`]"
    @click="e => this.$emit('click', e)"
    @dblclick="e => this.$emit('dblclick', e)"
  >
    <b-i v-if="icon" :icon="icon" :class="{ 'mr-1' : $slots.default }" />
    <slot />
  </button>
</template>

<script>
export default {
  name: "b-button",
  props: {
    variant : { default: "light" },
    type    : { default: "button" },
    block   : { default: false },
    shadow  : { default: false },
    size    : { default: null },
    icon    : { default: null },
  },
};
</script>