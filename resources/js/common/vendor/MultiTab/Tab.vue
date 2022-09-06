<template>
  <div class="tab h-100 w-100">
    <div class="tab-content h-100">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  name: "tab",
  props: ["name"],
};
</script>