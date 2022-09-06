<template>
  <b-menu :class="[{ 'ht-500': isNotEmpty }, `wd-${wXS ? 320 : 350}`]">
    <!-- header -->
    <template v-if="!loading" v-slot:header>
      <div class="p-3">
        <h5 class="flex-1" v-text="ucFirst($t('ال') + $t('notifications'))" />
        <!-- unread notifictaions count -->
        <template v-if="isNotEmpty">
          <div
            v-if="unreadNotificationsCount"
            class="fs-3 mb-1"
            v-text="`${unreadNotificationsCount} ${$t('unreadX', {attr: $t('notifications')})}`"
          />
          <div
            v-else
            class="fs-3"
            v-text="$t('noYet', { attr: $store.getters['locales/locale'] == 'ar' ? `${$t('notifications')} ${$t('unread') + $t('ة')}`: `${$t('unread')} ${$t('notifications')}` })"
          />
        </template>
      </div>
    </template>

    <!-- body (notifications) -->
    <template v-if="!loading" v-slot:body>
      <div class="flex-1" v-if="isNotEmpty">
        <notification
          v-for="id in orderedNotificationsIDS"
          :key="id"
          :notification="notifications[id]"
          class="border-bottom border-light"
        />
      </div>
      <div v-else class="flex-1 d-flex-center h-100 text-secondary p-2 bg-light">
        <div v-text="ucFirst($t('noYet', { attr: $t('notifications') }))" />
      </div>
    </template>

    <!-- loading -->
    <template v-else v-slot:body>
      <div class="h-100 py-4 d-flex-center">
        <clip-loader />
      </div>
    </template>

    <!-- footer (buttons) -->
    <template v-slot:footer v-if="isNotEmpty && !loading">
      <div class="bg-light d-flex align-items-center fs-3 p-1">
        <!-- mark all as seen -->
        <b-button
          variant="light"
          class="border-0 py-0 px-2"
          :title="$t('markAllAsRead')"
          @click="markAllAsSeen"
        >
          <b-i icon="tasks" />
        </b-button>
        <!-- see all -->
        <b-router
          :to="'/'"
          class="btn btn-light border-0 flex-1 pl-2 py-0 pr-4"
          :title="$t('viewAll')"
          v-t="'viewAll'"
        />
      </div>
    </template>
  </b-menu>
</template>

<script>
import { mapGetters } from "vuex";
const Notification = () => import("../components/Notification.vue");
export default {
  name: "notifications-menu",
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      notifications: "notifications/notifications",
      orderedNotificationsIDS: "notifications/orderedNotificationsIDS",
      unreadNotificationsCount: "notifications/unreadNotificationsCount",
    }),
    isNotEmpty() {
      return Object.keys(this.notifications).length;
    },
  },
  created: async function () {
    this.loading = true;
    await this.$store.dispatch("notifications/fetchNotifications");
    this.loading = false;
  },
  methods: {
    markAllAsSeen: function () {
      this.$store.dispatch("notifications/markNotificationAsSeen").catch((errors) => console.log(errors));
    },
  },
  components: {
    Notification,
  },
};
</script>