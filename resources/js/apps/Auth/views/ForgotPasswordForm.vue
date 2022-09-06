<template>
  <auth-form>
    <template v-slot:form>
      <!-- loading -->
      <clip-loader v-if="loading"></clip-loader>

      <!-- send reset password link -->
      <form @submit.prevent="sendResetPasswordLink" v-else-if="!sent">
        <!-- instruction -->
        <p class="text-dark-8 font-md" v-t="'forgotPassword.msg'"></p>

        <!-- Email -->
        <b-input
          type="email"
          class="form-control rounded-edges my-4"
          v-model="form.email"
          :class="{ 'is-invalid': errors.email }"
          :placeholder="$t('forgotPassword.input')"
          required
        />

        <!-- errors -->
        <b-error
          class="my-4"
          :field="errors.email"
          t="forgotPassword"
          attr="email"
        />

        <!-- submit button -->
        <b-button
          type="submit"
          variant="primary"
          class="rounded-edges px-4"
          v-t="'forgotPassword.btn'"
        />
      </form>

      <!-- link sent ! -->
      <div class="text-center text-primary" v-else>
        <b-i icon="check-circle" size="5x" class="d-block mb-3" />
        <p
          class="font-bold text-capitalize"
          v-t="'forgotPassword.linkSent'"
        />
        <p
          class="d-block mt-2 text-dark font-md"
          v-html="$t('forgotPassword.haveALook')"
        />
      </div>
    </template>
    <template v-slot:footer>
      <!-- back to login -->
      <b-router
        class="d-block text-primary"
        :to="{ name: 'login' }"
        v-html="$t('backTo', { attr: $t('login') })"
      />
    </template>
  </auth-form>
</template>

<script>
const AuthForm = () => import("../../../common/masters/Auth/AuthForm.vue");
export default {
  name: "forgot-password-form",
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
  components: {
    AuthForm,
  },
};
</script>