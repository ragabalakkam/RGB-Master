<template>
  <div class="client-master vh-100 d-flex flex-column">
    <!-- header -->
    <header class="bg-white border-bottom">
      <navbar class="py-2" />
    </header>

    <!-- main -->
    <main class="flex-1 d-flex overflow-scroll">
      <sidebar v-model="sidebar_collapsed" :links="links" class="border-right bg-white" style="z-index:99999" />
      <div class="d-flex flex-column flex-1">
        <router-view
          class="flex-1"
          style="overflow-y:scroll"
          :key="this.$route.fullPath"
        />
        <!-- <footer class="bg-white border-top">
          <footage />
        </footer> -->
      </div>
    </main>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Sidebar from './Layouts/Sidebar.vue';
import Navbar from "./Layouts/Navbar.vue";
import Footage from "./Layouts/Footage.vue";
export default {
  name: "client-master",
  props: ['links'],
  data() {
    return {
      sidebar_collapsed: false,
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
      app: "app",
    }),
  },
  components: {
    Navbar,
    Footage,
    Sidebar,
  },
};
</script>

<style lang="scss" scoped>
.client-master {
    --sdbr-width: 18.5rem;
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