<template>
  <div
    v-if="(!link.hasOwnProperty('if') || link.if) && hasAnyPermission"
    class="dropdown position-relative"
  >
    <!-- if : link -->
    <b-router v-if="link.to" :to="link.to" :class="_titleClass">
      <b-i v-if="showIcons && link.icon" :icon="link.icon" />
      <p class="flex-1 nowrap text-capitalize hidden-when-collapsed" v-text="link.text" @click="wXS ? $emit('collapsed') : null" />
      <b-i v-if="link.dropdown && link.dropdown.length" icon="chevron-double-down" class="fs-2 font-300 hidden-when-collapsed" />
    </b-router>

    <!-- else if : text -->
    <div v-else :class="_titleClass">
      <b-i v-if="showIcons && link.icon" :icon="link.icon" />
      <p class="flex-1 nowrap text-capitalize hidden-when-collapsed" v-text="link.text" />
      <b-i v-if="link.dropdown && link.dropdown.length" icon="chevron-double-down" class="fs-2 font-300 hidden-when-collapsed" />
    </div>

    <!-- dropdown -->
    <div v-if="link.dropdown" :class="wXS ? 'p-2' : 'position-absolute px-4'">
      <div class="bg-sidebar rounded-lg shadow">
        <dropdown
          class="border-bottom border-sidebar"
          v-for="(dropdownLink, i) in link.dropdown"
          :key="i"
          :link="dropdownLink"
          @collapsed="$emit('collapsed')"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "dropdown",
  props: {
    link        : { type: Object, default: () => {} },
    showIcons   : { default: true },
    showText    : { default: true },
    titleClass  : { default: '' },
  },
  computed: {
    ...mapGetters({
      routes_permissions: 'permissions/routes_permissions',
    }),
    _titleClass() {
      return `sidebar-title btn btn-sidebar text-left d-flex align-items-center flex-gap-2 ${this.titleClass}`;
    },
    hasAnyPermission() {
      return this.validateLink(this.link) && (!this.link.dropdown || this.link.dropdown.find(link => this.validateLink(link)));
    },
  },
  methods: {
    validateLink: function (link) {
      return !link.to
        || !this.routes_permissions.hasOwnProperty(link.to.name)
        || this.can(this.routes_permissions[link.to.name]);
    },
  },
};
</script>

<style lang="scss" scoped>
.sidebar-title {
  + div {
    display: none;

    &:hover {
      display: block;
      color: var(--sdbr-fg-hover);
    }
  }

  &:hover {
    color: var(--sdbr-fg-hover);
    background-color: var(--sdbr-bg-hover);
  }

  &:hover + div {
    display: block;
  }

  + .position-absolute {
    min-width: 100%;
  }
}

// dropdown absolute position

.sidebar-h .dropdown > .sidebar-title + .position-absolute {
  top: 100%;
}
.sidebar-v .dropdown > .sidebar-title + .position-absolute {
  top: 0;
}

html[dir=rtl] {
  .sidebar-h .dropdown > .sidebar-title + .position-absolute {
    left: 0;
  }
  .sidebar-v .dropdown > .sidebar-title + .position-absolute {
    right: 100%;
  }
}

html[dir=ltr] {
  .sidebar-h .dropdown > .sidebar-title + .position-absolute {
    right: 0;
  }
  .sidebar-v .dropdown > .sidebar-title + .position-absolute {
    left: 100%;
  }
}

// hover

.sidebar-title:hover i.fa-chevron-double-down{
  transform: rotate(90deg);
}

.tablet-view,
.pc-view {
  .sidebar-collapsed:not(:hover) .hidden-when-collapsed {
    display: none;
  }
}

// borders

// .dropdown:last-child {
//   border: 0;
// }

.router-link-exact-active {
  background: var(--sdbr-bg-active);
  color: var(--sdbr-fg-active);
}
</style>