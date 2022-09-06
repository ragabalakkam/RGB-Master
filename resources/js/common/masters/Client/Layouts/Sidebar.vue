<template>
  <aside class="d-flex flex-column sidebar-border p-3" :class="{ 'sidebar-collapsed' : collapsed }">

    <!-- current organization name (if exists) and back arrow -->
    <div v-if="organization" class="mb-3 d-flex flex-gap-2 align-items-center">
      <span class="flex-1 fs-5 hidden-when-collapsed pr-4" v-text="parseName(organization.name)" />
      <b-router
        :style="wXS ? '' : 'margin: 0 -1.8rem;'"
        :to="{ name: 'organizations.index' }"
        class="border-0 bg-white p-0 fs-6 w-max-content text-secondary-5 text-hover-dark"
      >
        <b-i icon="arrow-circle-left" />
      </b-router>
    </div>

    <!-- dropdown links -->
    <div class="flex-1 d-flex flex-column flex-gap-2">
      <dropdown
        v-for="(link, i) in links"
        :key="i"
        :link="link"
        :title-class="'border shadown-sm min-ht-30'"
      />
      <b-button class="bg-all-none border text-left text-danger-7 text-hover-danger font-600 d-flex align-items-center flex-gap-2" v-b-modal="{ name: 'logout' }">
        <b-i icon="sign-out" class="wd-30 text-center font-600" />
        <span class="hidden-when-collapsed" v-t="'logout'" />
      </b-button>
    </div>

    <!-- toggle collapse -->
    <b-button v-if="!wXS" icon="arrows-alt-h" class="border" @click="collapsed = !collapsed" />

  </aside>
</template>

<script>
import { mapGetters } from "vuex";
import Dropdown from './Dropdown.vue';
export default {
  components: { Dropdown },
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
      user: 'auth/user',
      permissions: "auth/permissions",
      icons: "icons",
      organizations: "client/organizations",
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
    organization() {
      let id = this.$route.params.org_id || this.$route.query.org_id;
      return id ? this.organizations[id] : null;
    },
  },
  created: function () {
    [
      'client/fetchApps',
      'client/fetchOrganizations',
    ].forEach(action => this.$store.dispatch(action))
  },
};
</script>

<style lang="scss" scoped>
aside {
  width: var(--sdbr-width);

  &.sidebar-collapsed {
    .hidden-when-collapsed {
      display: none;
    }
  }
}

.pc-view {
  aside {
    width: var(--sdbr-width);

    &.sidebar-collapsed {
      width: 5rem;

      .hidden-when-collapsed {
        display: none;
      }
    }
  }
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