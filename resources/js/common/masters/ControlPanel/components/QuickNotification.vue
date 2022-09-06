<template>
  <div
    v-if="notification" 
    class="quick-notification bg-white text-secondary text-hover-black rounded-xl shadow d-flex-center flex-gap-2 position-absolute position-top-right p-2 c-ptr wd-160"
    @click="
      moveToLink();
      notification.click ? notification.click() : null;
    "
  >
    <div style="flex:none">
      <i v-if="typeof notification.image == 'string'" :class="`${notification.image} text-${notification.variant || 'primary'}`" />
      <b-img v-else :src="parseImg(notification)" size="16" class="rounded-lg" />
    </div>
    <p
      class="fs-3 flex-1"
      v-text="$t('notification_contents.' + notification.content + '.title', args)"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "quick-notification",
  computed: {
    ...mapGetters({
      notification: "notifications/quickNotification",
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
  mounted() {
    setTimeout(() => this.$store.commit("notifications/CLEAR_QUICK_NOTIFICATION"), 1500);
  },
  methods: {
    moveToLink: function () {
      this.$store.commit("notifications/CLEAR_QUICK_NOTIFICATION");
      this.$store
        .dispatch("notifications/markNotificationAsSeen", this.notification.id)
        .then(() => {
          if (this.notification.url) {
            if (this.notification.url.includes("http"))
              window.open(this.notification.url);
            else if (
              this.notification.url &&
              this.notification.url.length &&
              this.notification.url !== this.$route.fullPath
            )
              this.$router.push(this.notification.url);
          }
        });
    },
  },
};
</script>

<style lang="scss">
.quick-notification {
  top: 2.8125rem;
  z-index: 9999;

  &::before {
    content: "";
    position: absolute;
    top: -0.625rem;
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid #eee;
    transform: translateX(-50%);
  }

  &::after {
    content: "";
    position: absolute;
    top: -0.5625rem;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 9px solid #fff;
    transform: translateX(-50%);
    z-index: 10000;
  }
}

html[dir="ltr"] {
  .pc-view .quick-notification {
    &::before,
    &::after {
      right: 10%;
    }
  }
  .phone-view,
  .tablet-view {
    .quick-notification {
      right: -3rem;
      &::before,
      &::after {
        right: 5.15rem;
        transform: translateX(50%);
      }
    }
  }
}

html[dir="rtl"] {
  .pc-view .quick-notification {
    &::before,
    &::after {
      left: 10%;
    }
  }
  .phone-view,
  .tablet-view {
    .quick-notification {
      left: -3rem;
      &::before,
      &::after {
        left: 5.15rem;
      }
    }
  }
}
</style>