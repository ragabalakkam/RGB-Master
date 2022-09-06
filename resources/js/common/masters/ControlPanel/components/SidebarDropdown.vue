<template>
  <div
    class="sidebar-dropdown overflow-hidden d-flex flex-column ht-40-not-collapsed"
    :class="isExpanded ? `ht-${(count + 1) * 40}` : 'ht-40'"
  >
    <div
      class="d-flex align-items-center c-ptr ht-40 px-2 px-md-3 bg-hover-secondary border-bottom border-dark"
      :class="{ 'text-primary' :isExpanded }"
      :style="isExpanded ? 'background-color: #171b21' : ''"
      @click="expanded = isExpanded ? null : title"
    >
      <p class="flex-1 align-items-center mb-0">
        <slot name="link" />
      </p>
      <b-i
        v-if="count"
        icon="angle-down"
        :class="{ 'rotate-90': !isExpanded }"
        class="hidden-when-collpased"
      />
    </div>
    <div :class="isExpanded ? 'bg-info-2' : 'overflow-hidden'" :style="isExpanded ? null : 'height: 0'" v-if="count">
      <slot name="dropdown" />
    </div>
  </div>
</template>

<script>
export default {
  name: "sidebar-dropdown",
  props: ["value", "collapsed", "count", "title"],
  computed: {
    expanded: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
    isExpanded() {
      return this.value === this.title;
    },
  },
};
</script>

<style lang="scss" scoped>
.rotate-90 {
  transform: rotate(90deg);
}
</style>