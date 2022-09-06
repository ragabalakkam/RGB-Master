<template>
  <div class="h-100 d-flex-center">
    <div
      class="bg-white p-4 min-wd-450 text-center"
      :class="wXS ? 'w-100 h-100' : 'rounded-lg shadow'"
    >
      <div class="text-center">
        <img src="/imgs/register.png" height="200" />
      </div>
      
      <div class="py-4" v-if="loading">
        <clip-loader />
      </div>

      <!-- send reset password link -->
      <form @submit.prevent="sendResetPasswordLink" v-else-if="!sent">
        <!-- instruction -->
        <p class="text-dark-8 font-md mb-3" v-t="'forgotPassword.msg'" />

        <!-- email -->
        <b-input
          type="email"
          name="email"
          class="rounded-edges"
          v-model="form.email"
          :errors="errors"
          :show-label="false"
          :show-error="false"
          :placeholder="$t('forgotPassword.input')"
          required
        />

        <!-- errors -->
        <b-error
          class="mt-3 mx-auto max-wd-320"
          :field="errors.email"
          t="forgotPassword"
          attr="email"
        />

        <!-- submit button -->
        <b-button
          type="submit"
          variant="primary"
          class="rounded-edges px-4 my-3"
          v-t="'forgotPassword.btn'"
        />
      </form>

      <!-- link sent ! -->
      <div class="text-center text-primary py-4" v-else>
        <b-i icon="check-circle" size="5x" class="d-block mb-3" />
        <p
          class="font-bold text-capitalize"
          v-t="'forgotPassword.linkSent'"
        />
        <p
          class="d-block my-2 text-dark font-md"
          v-html="$t('forgotPassword.haveALook')"
        />
      </div>

      <!-- back to login -->
      <b-router
        class="d-block text-primary"
        :to="{ name: 'login' }"
        v-html="$t('backTo', { attr: $t('login') })"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "auth.forgot_password",
  data() {
    return {
      form: {
        email: null,
      },
      loading: false,
      errors: {},
      sent: false,
    };
  },
  methods: {
    sendResetPasswordLink: async function () {
      this.loading = true;
      await this.$store
        .dispatch("auth/sendResetPasswordLink", this.form)
        .then(() => this.sent = true)
        .catch(e => this.errors = e);
      this.loading = false;
    },
  },
};
</script>