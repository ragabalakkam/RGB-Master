<template>
  <div class="control-panel-master d-flex h-100" :class="{ 'flex-column' : sidebarDir == 'h' }">
    <sidebar
      v-model="sidebarCollapsed"
      :dir="sidebarDir"
      :links="links"
      class="shadow border-sidebar"
      style="z-index: 1; flex: none;"
      :class="{ 'order-2' : sidebarDir == 'h' }"
      @dir="dir => sidebarDir = dir"
    />

    <div class="d-flex flex-column" :class="{ 'flex-1' : sidebarDir == 'v' }">
      <navbar
        class="py-2 border-bottom border-sidebar shadow"
        style="background-color: var(--sdbr-dark); color: var(--sdbr-fg);"
        @toggle-sidebar="sidebarCollapsed = !sidebarCollapsed"
        :collapsed="sidebarCollapsed"
      />
      <div class="flex-1 px-3 px-md-4" style="overflow-y: scroll">
        <slot v-if="sidebarDir == 'v'" />
      </div>
    </div>

    <div v-if="sidebarDir == 'h'" class="flex-1 order-3 overflow-scroll px-3 px-md-4">
      <slot />
    </div>
  </div>
</template>

<script>
import Widget from './components/Widget.vue';
import Navbar from "./Layouts/Navbar.vue";
import Sidebar from "./Layouts/Sidebar.vue";
export default {
  name: "control-panel-master",
  props: ['links'],
  data() {
    return {
      sidebarCollapsed: false,
      sidebarDir: 'v',
    };
  },
  components: {
    Sidebar,
    Navbar,
    Widget,
  },
};
</script>

<style lang="scss" scoped>
.control-panel-master {
  --sdbr-dark: var(--bs-light);
  --sdbr-bg: #eff0f1;
  --sdbr-fg: var(--bs-dark);
  --sdbr-bg-hover: var(--bs-info);
  --sdbr-fg-hover: var(--bs-white);
  --sdbr-bg-active: var(--bs-primary);
  --sdbr-fg-active: var(--bs-white);
  --sdbr-bd: #e5e5e5;
}
</style>