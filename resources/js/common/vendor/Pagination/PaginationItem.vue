<template>
  <div class="pagination-item" @click="$emit('click')">
    <slot />
  </div>
</template>

<script>
export default {
  name: "pagination-item",
};
</script>