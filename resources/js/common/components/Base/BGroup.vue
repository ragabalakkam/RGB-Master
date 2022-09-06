<template>
  <div class="b-group position-unset">
    <div class="rounded-lg border border-secondary-2" style="margin-top: 1rem">
      <p
        class="w-max-content px-2 bg-white fs-4 ml-2"
        :class="titleClass"
        style="margin-top: -1rem"
        v-text="ucFirst(title)"
      />
      <div class="p-3">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "b-group",
  props: ["title", "titleClass"],
};
</script>