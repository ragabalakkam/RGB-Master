<template>
  <div
    v-if="!link.hasOwnProperty('if') || link.if"
    class="dropdown position-relative"
  >
    <!-- if : link -->
    <b-router v-if="link.to" :to="link.to" :class="_titleClass">
      <b-i
        v-if="showIcons && link.icon"
        :icon="link.icon"
        class="wd-30 fs-5 text-center"
      />
      <p
        class="flex-1 nowrap text-capitalize hidden-when-collapsed"
        v-text="link.text"
        @click="wXS ? $emit('collapsed') : null"
      />
      <b-i
        v-if="link.dropdown && link.dropdown.length"
        icon="chevron-double-down"
        class="fs-2 p-2 font-300 hidden-when-collapsed"
        @click.prevent.stop="collapsed = !collapsed"
      />
    </b-router>

    <!-- else if : text -->
    <div v-else :class="_titleClass">
      <b-i
        v-if="showIcons && link.icon"
        :icon="link.icon"
        class="wd-30 fs-5 text-center"
      />
      <p
        class="flex-1 nowrap text-capitalize hidden-when-collapsed"
        v-text="link.text"
      />
      <b-i
        v-if="link.dropdown && link.dropdown.length"
        icon="chevron-double-down"
        class="fs-2 font-300 hidden-when-collapsed"
      />
    </div>

    <!-- dropdown -->
    <div v-if="link.dropdown" :class="wXS ? '' : ''">
      <div class="bg-sidebar rounded-lg border bg-light" style="margin-top:-1px; border-right-width:0.5rem;">
        <dropdown
          v-for="(dropdownLink, i) in link.dropdown"
          :key="i"
          :link="dropdownLink"
          :class="{ 'border-bottom': i !== link.dropdown.length - 1 }"
          class="border-sidebar"
          @collapsed="$emit('collapsed')"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "dropdown",
  props: {
    link: { type: Object, default: () => {} },
    showIcons: { default: true },
    showText: { default: true },
    titleClass: { default: "" },
  },
  data() {
    return {
      collapsed: true,
    };
  },
  computed: {
    _titleClass() {
      return `sidebar-title ${this.collapsed ? '' : 'decollapsed'} btn btn-sidebar text-left d-flex align-items-center no-shadow flex-gap-2 ${this.titleClass}`;
    },
  },
};
</script>

<style lang="scss" scoped>
.sidebar-title {
  + div {
    display: none;

    &.decollapsed {
      display: block;
      color: var(--sdbr-fg-hover);
    }
  }

  &.decollapsed {

    + div {
      display: block;
    }
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

html[dir="rtl"] {
  .sidebar-h .dropdown > .sidebar-title + .position-absolute {
    left: 0;
  }
  .sidebar-v .dropdown > .sidebar-title + .position-absolute {
    right: 100%;
  }
}

html[dir="ltr"] {
  .sidebar-h .dropdown > .sidebar-title + .position-absolute {
    right: 0;
  }
  .sidebar-v .dropdown > .sidebar-title + .position-absolute {
    left: 100%;
  }
}

// hover

.sidebar-title:hover i.fa-chevron-double-down {
  transform: rotate(90deg);
}

.tablet-view,
.pc-view {
  .sidebar-collapsed .hidden-when-collapsed {
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

.phone-view,
.tablet-view {
  aside {
    width: 5rem;

    .hidden-when-collapsed {
      display: none;
    }
  }
}
</style>