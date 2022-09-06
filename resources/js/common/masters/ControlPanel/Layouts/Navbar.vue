<template>
  <nav class="d-flex align-items-center">
    <div class="px-2 px-lg-3 d-flex w-100">
      <button
        class="btn btn-light bg-all-none rounded-circle border-0 mr-2 no-shadow text-inherit text-focus-info"
        @click="$emit('toggle-sidebar')"
      >
        <b-i :icon="collapsed ? 'times' : 'align-left'" />
        <!-- :class="`text-${collapsed ? 'dark' : 'secondary'}`" -->
      </button>
      
      <div>
        <!-- <form
          v-if="!wXS"
          @submit.prevent="search"
          @reset.prevent="search_query = null"
          class="form-control rounded-edges bg-light p-0 w-max-content d-flex align-items-center overflow-hidden"
        >
          <div class="position-relative flex-1">
            <b-form-input
              type="text"
              class="rounded-0 border-0 bg-none flex-1 wd-190 bg-all-none"
              :placeholder="`${$t('navbarSearch')} ...`"
              v-model="search_query"
            />
            <b-button
              v-show="search_query"
              variant="light"
              type="reset"
              class="position-absolute position-right sz-15 p-0 d-flex-center rounded-circle border-0"
            >
              <b-i icon="times" class="fs-3" />
            </b-button>
          </div>
          <b-button
            variant="light"
            :class="`text-${
              search_query ? 'primary' : 'secondary'
            } text-hover-${search_query ? 'primary' : 'secondary'}`"
            class="p-0 d-flex-center sz-25 rounded-circle mr-2 bg-all-none border-0"
          >
            <b-i icon="search" />
          </b-button>
        </form> -->
      </div>
      
      <div class="flex-1">
        <div class="d-flex float-right">

          <!-- languages menu -->
          <div class="dropdown mx-2 d-flex-center" :title="$t('ال') + $t('language')">
            <b-button
              variant="light"
              class="sz-35 bg-all-none border-0 p-0 rounded-circle fs-5 text-inherit d-flex-center no-shadow"
              id="languages-dropdown-menu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <img :src="`/imgs/flags/${getLocale()}.jpg`" class="wd-25 rounded-flag" />
            </b-button>
            <languages-menu aria-labelledby="languages-dropdown-menu" />
          </div>

          <!-- home button -->
          <div class="dropdown mx-2 d-flex-center" :title="$t('home')">
            <b-router to="/" class="sz-35 bg-all-none border-0 p-0 rounded-circle fs-5 text-inherit d-flex-center no-shadow">
              <b-i icon="home-alt" />
            </b-router>
          </div>

          <!-- messages menu (currently unused) -->
          <!-- <div class="dropdown mx-2 d-flex-center" :title="$t('ال') + $t('messages')">
            <b-button
              variant="light"
              class="sz-35 bg-all-none border-0 p-0 rounded-circle fs-5 text-inherit d-flex-center no-shadow"
              id="messages-dropdown-menu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <b-i icon="envelope" />
              <pulse variant="danger" />
            </b-button>
            <messages-menu aria-labelledby="messages-dropdown-menu" />
          </div> -->

          <!-- notifications menu -->
          <div class="dropdown mx-2 d-flex-center" :title="$t('ال') + $t('notifications')">
            <b-button
              variant="light"
              class="sz-35 bg-all-none border-0 p-0 rounded-circle fs-5 text-inherit d-flex-center no-shadow"
              id="notifications-dropdown-menu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              @click="$store.commit('notifications/CLEAR_QUICK_NOTIFICATION')"
            >
              <b-i icon="bell" />
              <pulse variant="success" class="m-2" v-if="unreadNotificationsCount" />
            </b-button>
            <notifications-menu class="notifications-menu" aria-labelledby="notifications-dropdown-menu" />
            <quick-notification />
          </div>

          <!-- fullscreen -->
          <div class="dropdown mx-2 d-flex-center" v-if="wMD || wLG" :title="$t(fullscreen ? 'closeX' : 'openX', { attr: $t('fullscreen')})">
            <b-button
              variant="light"
              class="sz-35 bg-all-none border-0 p-0 rounded-circle fs-4 text-inherit d-flex-center no-shadow"
              @click="() => setFullscreen(fullscreen ? false : true)"
            >
              <b-i :icon="fullscreen ? 'compress' : 'expand'" />
            </b-button>
          </div>

          <!-- user settings menu -->
          <div class="dropdown mx-2 d-flex-center" :title="$t('X', {0: $t('menu'), 1: $t('settings') })">
            <button
              variant="light"
              class="rounded-circle bg-none border-0 p-0 border shadow-sm"
              id="settings-dropdown-menu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <b-img :src="img(user.image)" size="30" circle v-if="user" />
            </button>
            <settings-menu aria-labelledby="settings-dropdown-menu" />
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from "vuex";
import Pulse from "../components/Pulse.vue";
import QuickNotification from '../components/QuickNotification.vue';
import LanguagesMenu from "../Menus/LanguagesMenu.vue";
import MessagesMenu from "../Menus/MessagesMenu.vue";
import NotificationsMenu from "../Menus/NotificationsMenu.vue";
import SettingsMenu from "../Menus/SettingsMenu.vue";
export default {
  name: "navbar",
  props: ["collapsed"],
  data() {
    return {
      search_query: null,
      loadingNotifications: false,
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
      unreadNotificationsCount: "notifications/unreadNotificationsCount",
      fullscreen: "fullscreen",
    }),
  },
  components: {
    MessagesMenu,
    NotificationsMenu,
    SettingsMenu,
    LanguagesMenu,
    Pulse,
    QuickNotification,
  },
};
</script>

<style lang="scss" scoped>
@import "../../../../../sass/mixins/dir";
@include dir {
  .phone-view {
    .notifications-menu {
      #{$right}: -60% !important;
    }
    .messages-menu {
      #{$right}: -200% !important;
    }
    .languages-menu {
      #{$right}: -155% !important;
    }
  }
}
</style>