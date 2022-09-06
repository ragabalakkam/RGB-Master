<template>
  <aside
    class="d-flex flex-column-v flex-gap-3 px-3 sidebar-border"
    :class="[
      { 'align-items-center': direction == 'h' },
      { 'sidebar-collapsed': collapsed },
      `sidebar-${direction}`,
      `border-${direction == 'v' ? 'right' : 'bottom'}`
    ]"
    :style="`background: linear-gradient(to bottom right, var(--sdbr-dark) 0%, var(--sdbr-bg) 100%);`"
  >
    <!-- logo & dir toggler -->
    <div
      class="sidebar-logo d-flex align-items-center flex-gap-2 ht-50"
      :class="{ 'border-bottom border-sidebar': direction == 'v' }"
    >
      <b-router class="flex-1 d-flex-center flex-gap-2" to="/" v-if="direction == 'v'">
        <b-img class="rounded" :size="collapsed ? 40 : 30" :src="`/storage/${app.logo}`" />
        <p class="flex-1 hide-when-collapsed fs-5" v-text="abbr(parseName(app.name), 20, '')" />
      </b-router>
      <b-button
        v-if="false && !wXS"
        class="btn btn-sidebar sz-30 text-sidebar d-flex-center p-0 fs-3 hide-when-collapsed"
        @click="direction = direction == 'h' ? 'v' : 'h'"
      >
        <b-i icon="bars" />
      </b-button>
    </div>

    <!-- search -->
    <!-- <b-input v-model="search" :placeholder="$t('search')" /> -->

    <!-- dropdown links -->
    <div class="flex-1">
      <div class="sidebar-dropdown-links d-flex flex-column-v no-scrollbars">
        <dropdown
          v-for="(link, i) in links"
          :key="i"
          :link="link"
          :title-class="direction == 'h' ? 'py-1' : 'border-0'"
          @collapsed="collapsed = !collapsed"
        />
      </div>
    </div>
  </aside>
</template>

<script>
import { mapGetters } from "vuex";
import Dropdown from ".//Dropdown.vue";
export default {
  name: "sidebar",
  props: ['value', 'dir', 'backgroundColor', 'foregroundColor', 'borderColor', 'links'],
  data() {
    return {
      search: null,
    };
  },
  computed: {
    ...mapGetters({
      app: "configurations/app",
      permissions: "auth/permissions",
      icons: "icons",
    }),
    collapsed: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
    direction: {
      get() {
        return this.dir;
      },
      set(val) {
        this.$emit('dir', val);
      },
    },
  },
  components: {
    Dropdown,
  },
};
</script>

<style lang="scss">
aside {
  background-color: var(--sdbr-bg);
  color: var(--sdbr-fg);

  //

  .btn-sidebar {
    background-color: var(--sdbr-bg);
    color: var(--sdbr-fg);
    // border: 1px solid var(--sdbr-bd);
  }

  //

  &.sidebar-v {
    height: 100%;
    width: 16rem;

    .dropdown {
      border-bottom: 1px solid var(--sdbr-bd);
    }

    .flex-column-v,
    &.flex-column-v {
      flex-direction: column;
    }

    &.sidebar-collapsed:not(:hover) {
      width: 4.5rem;
    }

    .sidebar-dropdown-links > .dropdown {
      margin-top: var(--bs-spacer-1);
      padding-bottom: var(--bs-spacer-1);
    }
  }

  &.sidebar-h {
    height: max-content !important;
    width: 100%;
    // padding-left: var(--bs-spacer-5);
    // padding-right: var(--bs-spacer-5);

    // .dropdown {
    //   border-left: 1px solid var(--sdbr-bd);
    // }

    .flex-column-h,
    &.flex-column-h {
      flex-direction: column;
    }

    .sidebar-dropdown-links {
      gap: var(--bs-spacer-2);
    }
  }
}

.phone-view aside.sidebar-v.sidebar-collapsed {
  width: 16rem;
}

.pc-view,
.tablet-view 
{
  .sidebar-collapsed:not(:hover) .hide-when-collapsed {
    display: none;
  }
}

.bg-sidebar {
  background-color: var(--sdbr-bg);
}
.text-sidebar {
  color: var(--sdbr-fg);
}
.border-sidebar {
  border-color: var(--sdbr-bd) !important;
}

html[dir=ltr] .phone-view aside:not(.sidebar-collapsed) {
  margin-left: -16rem;
}
html[dir=rtl] .phone-view aside:not(.sidebar-collapsed) {
  margin-right: -16rem;
}

// .overflow-y-scroll {
//   overflow-y: scroll;
//   overflow-x: hidden;
  
// }

// .overflow-x-scroll {
//   overflow-x: scroll;
//   overflow-y: hidden;
// }

// ::-webkit-scrollbar {
//   width: var(--bs-spacer-2);
// }
// ::-webkit-scrollbar-track {
//   background: var(--bs-light);
// }
// ::-webkit-scrollbar-thumb {
//   background: #f1f1f1;
// }
// ::-webkit-scrollbar-thumb:hover {
//   background: var(--bs-secondary);
// }
</style>