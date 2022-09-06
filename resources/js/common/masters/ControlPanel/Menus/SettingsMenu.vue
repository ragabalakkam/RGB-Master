<template>
  <b-menu class="settings-menu rounded-xl wd-220" v-if="user">
    <template v-slot:header>
      <div class="bg-primary px-4 py-3 d-flex align-items-center flex-gap-2">
        <div class="sz-40 bg-white rounded-circle overflow-hidden border shadow-sm">
          <b-img :src="img(user.image)" alt="user-avatar" size="40" />
        </div>
        <div class="flex-1">
          <p class="font-bold mb-0" v-text="user.name" />
          <small v-text="user.email" />
        </div>
      </div>
    </template>

    <template v-slot:body>
      <div class="p-2">
        <b-router to="/profile" :class="btnClass">
          <b-i icon="user" class="wd-25 mr-2" />
          <span class="text-capitalize" v-t="'profile'" />
        </b-router>
        <b-router v-if="user && user.role == 'employee' && can('adminPanel.access')" :class="btnClass">
          <b-i class="wd-25 mr-2" icon="analytics" />
          <span v-t="'controlPanel'" />
        </b-router>
        <!-- <b-router to="/profile" :class="btnClass">
          <b-i icon="cogs" class="wd-25 mr-2" />
          <span class="text-capitalize" v-text="$t('ال') + $t('settings')" />
        </b-router>
        <b-router to="/profile" :class="btnClass">
          <b-i icon="inbox" class="wd-25 mr-2" />
          <span class="text-capitalize" v-t="'inbox'" />
        </b-router>
        <b-router to="/profile" :class="btnClass">
          <b-i icon="envelope" class="wd-25 mr-2" />
          <span class="text-capitalize" v-t="'messages'" />
        </b-router> -->
        <b-button class="text-danger text-hover-danger text-focus-danger" :class="btnClass" v-b-modal="{ name: 'logout' }">
          <b-i icon="sign-out-alt" class="wd-25 mr-2" />
          <span class="text-capitalize" v-t="'logout'" />
        </b-button>
      </div>
    </template>
  </b-menu>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "settings-menu",
  data() {
    return {
      btnClass: "btn btn-light btn-block py-1 d-flex align-items-center bg-white border-0 bg-hover-light",
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
    }),
  },
};
</script>