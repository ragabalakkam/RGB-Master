<template>
  <div class="default-master vh-100 d-flex flex-column">
    <!-- header -->
    <header
      :class="
        $route.name == 'home-page' 
        ? 'position-absolute position-top-left w-100 index-up text-white'
        : 'bg-white border-bottom border-x shadow'
      "
    >
      <navbar class="container" />
    </header>

    <!-- main -->
    <main class="flex-1 overflow-scroll">
      <router-view class="min-h-100" :key="this.$route.fullPath" />
    </main>

    <!-- footer -->
    <!-- <footer class="bg-white border-top border-x">
      <footage />
    </footer> -->
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const Navbar = () => import("./Layouts/Navbar");
const Footage = () => import("./Layouts/Footage");
export default {
  name: "default-master",
  computed: {
    ...mapGetters({
      user: "auth/user",
      app: "app",
    }),
  },
  components: {
    Navbar,
    Footage,
  },
};
</script>