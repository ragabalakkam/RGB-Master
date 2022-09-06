<template>
  <nav class="navbar navbar-expand-lg navbar-light transition-all-0" :style="`justify-content: space-between; ${$route.name == 'home-page' ? 'color: var(--home-color)' : ''}`">
    <!-- brand logo & name -->
    <b-router to="/" class="navbar-brand d-flex align-items-center p-0">
      <b-img :src="`/storage/${info.logo}`" size="30" class="mr-2 rounded" />
      <p class="fs-4" v-text="parseName(info.name)" />
    </b-router>

    <!-- user buttons / login -->
    <ul class="d-flex align-items-center flex-gap-3 list-unstyled mb-0" :class="`order-${wXS ? 2 : 3}`">
      <!-- languages menu -->
      <div class="dropdown d-flex-center" :title="$t('ال') + $t('language')">
        <b-button
          variant="light"
          class="sz-35 border-0 py-0 px-2 rounded-circle fs-5 text-secondary d-flex-center bg-all-none"
          id="languages-dropdown-menu"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <img
            :src="`/imgs/flags/${getLocale()}.jpg`"
            class="wd-30 rounded-flag"
          />
        </b-button>
        <languages-menu aria-labelledby="languages-dropdown-menu" />
      </div>

      <!-- if user => user buttons -->
      <template v-if="user">
        <!-- notifications menu -->
        <div class="dropdown d-flex-center">
          <b-button
            variant="light"
            class="sz-35 bg-none border-0 p-0 rounded-circle fs-5 d-flex-center text-inherit bg-hover-light"
            id="notifications-dropdown-menu"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            @click="$store.commit('notifications/CLEAR_QUICK_NOTIFICATION')"
          >
            <b-i icon="bell" class="font-300" />
            <pulse
              variant="success"
              class="m-2"
              v-if="unreadNotificationsCount"
            />
          </b-button>
          <notifications-menu
            class="notifications-menu"
            aria-labelledby="notifications-dropdown-menu"
          />
          <quick-notification />
        </div>

        <!-- user name & image -->
        <li v-if="user" class="nav-item dropdown">
          <a
            id="navbarDropdown"
            role="button"
            class="nav-link d-flex-center p-0 bg-hover-light rounded-edges"
            :class="{ 'dropdown-toggle pr-2': !wXS }"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <b-img :src="parseImg(user)" size="30" circle />
            <span v-text="user.name" class="ml-2 mr-1 pt-1 hidden-xs" />
          </a>
          <div class="dropdown-menu px-2 text-capitalize text-dark" aria-labelledby="navbarDropdown">
            <b-router v-if="can('adminPanel.access')" to="/master/dashboard" class="dropdown-item py-1 px-2">
              <b-i class="mr-2 wd-15" icon="analytics" />
              <span v-t="'controlPanel'" />
            </b-router>
            <b-router v-else-if="user.role == 'customer'" to="/client/dashboard" class="dropdown-item py-1 px-2">
              <b-i class="mr-2 wd-15" icon="analytics" />
              <span v-t="'controlPanel'" />
            </b-router>

            <b-router class="dropdown-item py-1 px-2" to="/profile">
              <b-i class="mr-2 wd-15" icon="address-book" />
              <span v-t="'profile'" />
            </b-router>

            <div class="dropdown-divider my-1" />

            <button
              class=" btn btn-outline-danger border-0 dropdown-item px-2 text-danger"
              v-b-modal="{ name: 'logout' }"
            >
              <b-i class="mr-2 wd-15" icon="sign-out-alt" />
              <span class="text-capitalize" v-t="'logout'" />
            </button>
          </div>
        </li>
      </template>

      <!-- else => login -->
      <li class="nav-item" v-else>
        <b-router to="/auth/login" class="btn btn-primary py-1 rounded-edges" v-t="'login'" />
      </li>
    </ul>
  </nav>
</template>

<script>
import { mapGetters } from "vuex";
import QuickNotification from '../../ControlPanel/components/QuickNotification.vue';
const Pulse = () => import("../../ControlPanel/components/Pulse.vue");
const LanguagesMenu = () => import("../../ControlPanel/Menus/LanguagesMenu.vue");
const NotificationsMenu = () => import("../../ControlPanel/Menus/NotificationsMenu.vue");
export default {
  name: "navbar",
  computed: {
    ...mapGetters({
      app: "app",
      user: "auth/user",
      info: "configurations/app",
      unreadNotificationsCount: "notifications/unreadNotificationsCount",
    }),
  },
  components: {
    Pulse,
    LanguagesMenu,
    NotificationsMenu,
    QuickNotification,
  },
};
</script>

<style lang="scss" scoped>
html[dir="ltr"] {
  .phone-view .notifications-menu {
    right: -215% !important;
  }
}
html[dir="rtl"] {
  .phone-view .notifications-menu {
    left: -215% !important;
  }
}
.nav-link.router-link-exact-active.router-link-active {
  background-color: var(--bs-primary);
  color: var(--bs-white);
}
.nav-link.text-light {
  color: var(--bs-light);
}
.navbar-brand {
  &:hover,
  &:focus {
    color: var(--bs-light);
  }
}
.navbar-light .navbar-brand {
  color: inherit;
}
</style>
