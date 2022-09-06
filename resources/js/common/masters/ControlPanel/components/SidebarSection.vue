<template>
  <div class="sidebar-section">
    <header v-if="false && section.title">
      <p class="text-uppercase fs-3 ht-20 bg-secondary hidden-when-collpased px-2 px-md-3 d-flex-center" :title="section.title" v-text="section.title" />
      <hr class="show-when-collapsed" />
    </header>

    <main>
      <ul class="list-unstyled">
        <sidebar-dropdown
          v-for="(dropdown, index) in section.dropdowns"
          :key="index"
          :collapsed="collapsed"
          :count="Object.values(dropdown.links || {}).filter(l => !l.hasOwnProperty('if') || l.if).length"
          :title="dropdown.title"
          v-model="expanded"
        >
          <template v-slot:link>
            <div v-if="dropdown.links && Object.keys(dropdown.links).length" class="font-lg" :title="dropdown.title">
              <b-i :icon="dropdown.icon" class="text-center wd-25 fs-4" />
              <span class="text-capitalize hidden-when-collpased ml-1 mr-2" v-text="dropdown.title" />
            </div>
            <router-link
              v-else-if="!dropdown.hasOwnProperty('if') || dropdown.if"
              :to="dropdown.to"
              class="ht-40 font-lg bg-hover-secondary"
              :class="collapsed ? 'd-block' : 'd-flex align-items-center'"
            >
              <b-i :icon="dropdown.icon" class="text-center wd-25 fs-4" @click="onLinkClicked" />
              <span class="text-capitalize hidden-when-collpased ml-1 mr-2" v-text="dropdown.title" @click="onLinkClicked" />
              <!-- v-badge="dropdown.badge" -->
            </router-link>
          </template>
          <template v-slot:dropdown v-if="dropdown.links && Object.keys(dropdown.links).length">
            <div
              class="ht-40"
              v-for="(link, i) in Object.values(dropdown.links).filter(l => !l.hasOwnProperty('if') || l.if)"
              :key="i"
              :title="link.text"
              @click="onLinkClicked"
            >
              <router-link class="on-hover d-block flex-1 sidebar-link bg-hover-dark h-100 d-flex align-items-center" :class="{'disabled c-not-allowed' : link.disabled}" :to="link.to">
                <b-i :icon="link.disabled ? 'lock' : link.icon || 'chevron-double-right'" class="wd-15 ml-3 fs-3" />
                <span class="hidden-when-collpased pl-parent-hover-3" v-text="ucFirst(link.text)" />
                <!--  v-badge="dropdown.badge" -->
              </router-link>
            </div>
          </template>
        </sidebar-dropdown>
      </ul>
    </main>
  </div>
</template>

<script>
const SidebarDropdown = () => import("./SidebarDropdown");
export default {
  name: "sidebar-section",
  props: ["value", "section", "collapsed"],
  computed: {
    expanded: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
  },
  methods: {
    onLinkClicked: function () {
      return this.wXS && this.expanded
        ? this.$emit("collapse-sidebar")
        : () => {};
    },
  },
  components: {
    SidebarDropdown,
  },
};
</script>

<style lang="scss" scoped>
.sidebar-link.router-link-exact-active:not(.disabled) {
  background-color: #242d37; //var(--bs-light);
  // border-radius: var(--bs-spacer-1);
}
.sidebar-link.router-link-exact-active.disabled {
  cursor: not-allowed;
  // color: var(--bs-dark);
}
.on-hover:hover .pl-parent-hover-3 {
  padding-left: var(--bs-spacer-2);
}
</style>