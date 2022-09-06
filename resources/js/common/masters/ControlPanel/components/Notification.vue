<template>
  <div
    class="p-3 bg-hover-light-5 c-ptr flex-gap-3 d-flex align-items-center flex-gap-2"
    :class="{ 'bg-pink-1': !notification.read_at }"
    @click="markAsSeen"
  >
    <div class="rounded-xl overflow-hidden d-flex-center" style="flex:none">
      <i v-if="typeof notification.image == 'string'" :class="`${notification.image} fa-3x m-1 text-${notification.variant}`" />
      <b-img v-else :src="parseImg(notification)" />
    </div>
    <div class="flex-1">
      <div
        v-text="$t(`notification_contents.${notification.content}.title`, args)"
        :class="`text-dark${notification.read_at ? '-8' : ''}`"
      />
      <div v-if="notification.created_at" :class="`text-dark${notification.read_at ? '-8' : ''} font-sm my-1`">
        <b-i icon="calendar" class="mr-1" />
        <span v-text="castTime(notification.created_at)" />
      </div>
      <div
        class="fs-3"
        :class="`text-secondary${notification.read_at ? '-7' : ''}`"
        v-text="$t(`notification_contents.${notification.content}.content`, args)"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "notification",
  props: ["notification"],
  computed: {
    ...mapGetters({
      notifications: "notifications/notifications",
    }),
    args() {
      let args = this.notification.args,
        casts = this.notification.casts;
      Object.entries(args).forEach(arg => {
        if (casts[arg[0]]) args[arg[0]] = this[casts[arg[0]]](arg[1]);
      });
      return args;
    },
  },
  methods: {
    markAsSeen: function () {
      this.$store
        .dispatch("notifications/markNotificationAsSeen", this.notification.id)
        .then(() => {
          this.notification.url.includes("http")
            ? window.open(this.notification.url)
            : this.$router.push(this.notification.url);
        })
        .catch((errors) => console.log(errors));
    },
  },
};
</script>