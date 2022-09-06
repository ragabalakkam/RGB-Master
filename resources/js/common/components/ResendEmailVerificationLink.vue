<template>
  <div
    ref="resendEmailVerification"
    class="resend-email-verification-link text-light d-flex-center py-1 font-md font-900 ht-30"
    :class="`bg-${sent ? 'info' : 'primary open'}`"
  >
    <clip-loader v-show="loading" color="#fff" size="15px" class="pt-1" />
    <template v-if="!loading">
      <p class="mb-0" v-if="sent">
        <b-i icon="check-circle" class="mr-1" />
        {{ $t("mustVerify.sent") }} !
      </p>
      <template v-else>
        <span class="pl-2" v-t="'mustVerify.msg1'"></span>
        <span class="mx-2 mx-md-3">|</span>
        <span v-t="'mustVerify.didntReceive'"></span>
        <b-button
          variant="outline-light"
          size="sm"
          class="ml-3 font-md font-900 py-0"
          v-t="'mustVerify.btn'"
          @click="sendVerificationEmail"
        ></b-button>
      </template>
    </template>
  </div>
</template>

<script>
export default {
  name: "resend-email-verification-link",
  data() {
    return {
      sent: false,
      loading: false,
    };
  },
  mounted() {
    document.body.classList.add("has-verifying-bar");
  },
  methods: {
    sendVerificationEmail: async function () {
      this.loading = true;
      await this.$store.dispatch("auth/resendVerificationLink").then(() => {
        this.sent = true;
        document.body.classList.remove("has-verifying-bar");
      });
      this.loading = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.resend-email-verification-link {
  margin-top: -2rem;
  transition: margin var(--duration-med) ease-in-out 1.5s;
  &.open {
    margin-top: 0;
  }
}
</style>