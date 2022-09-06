<template>
  <div
    :class="`position-${position}`"
    class="position-top-left min-vh-100 w-100 bg-black-9 d-flex-center p-4 bd-blur-3"
    @click.self="$emit('close')"
  >
    <slot />
  </div>
</template>

<script>
export default {
  name: "b-popup",
  props: {
    position: { default: 'absolute' },
  },
};
</script>

<style>
.bd-blur-3 {
  backdrop-filter: blur(3px);
}
</style>