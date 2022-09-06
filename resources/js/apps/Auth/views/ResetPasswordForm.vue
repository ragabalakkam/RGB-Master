<template>
  <auth-form>
    <template v-slot:form>
      <clip-loader v-if="loading" />
      <template v-else>
        <form @submit.prevent="resetPassword" v-if="!done && !error">
          <!-- errors -->
          <div
            class="text-danger mb-3"
            v-if="Object.keys(errors).length"
            v-text="
              $t(
                `validation.${
                  errors.password
                    ? errors.password[0]
                    : errors.confirmPassword[0]
                }`,
                {
                  attr1: errors.password
                    ? $t('password')
                    : $t('confirmPassword'),
                }
              )
            "
          ></div>

          <!-- set password form -->
          <set-password-form
            :errors="errors"
            :passwords="{
              password: form.newPassword,
              confirmPassword: form.confirmNewPassword,
            }"
            v-model="validPasswords"
            @update-password="(val) => (form.newPassword = val)"
            @update-confirm-password="(val) => (form.confirmNewPassword = val)"
            @enter="$refs.submitFormBtn.click()"
            :name="'Mina Alfy'"
            class="font-md"
          />

          <!-- submit -->
          <b-button
            type="submit"
            variant="primary"
            class="rounded-edges px-4 mt-4"
            v-t="'resetPassword'"
            :disabled="!validPasswords"
            ref="submitFormBtn"
          ></b-button>
        </form>

        <div v-else-if="error" class="text-secondary">
          <b-i icon="times-circle" size="3x" class="d-block mb-3" />
          <p class="h4 mb-2">{{ $t("sorry") }},</p>
          <span
            v-text="
              $t('validation.status', { attr: $t('link'), status: $t(error) })
            "
          ></span>
        </div>

        <div class="text-center text-primary" v-else>
          <b-i icon="check-circle" size="5x" class="d-block mb-3"></b-i>
          <p class="mb-4 font-xl" v-html="$t('passwordHasBeenReset')"></p>
        </div>
      </template>
    </template>
    <template v-slot:footer>
      <router-link
        class="d-block text-primary font-md"
        :to="{ name: 'login' }"
        v-html="$t('backTo', { attr: $t('login') })"
      >
      </router-link>
    </template>
  </auth-form>
</template>

<script>
const AuthForm = () => import("../../../common/masters/Auth/AuthForm.vue");
const SetPasswordForm = () => import("../../../common/components/SetPasswordForm.vue");
export default {
  name: "reset-password-form",
  data() {
    return {
      form: {
        newPassword: null,
        confirmNewPassword: null,
      },
      validPasswords: false,
      errors: {},
      error: null,
      loading: false,
      token: false,
      done: false,
    };
  },
  async mounted() {
    this.loading = true;
    const token = this.$route.params.token;
    await axios
      .get(`/api/v1/auth/check-reset-password-token/${token}`)
      .then(() => (this.token = token))
      .catch((error) => {
        switch (error.response.status) {
          case 403:
            this.error = "expired";
            break;
          case 404:
            this.error = "invalid";
            break;
        }
      });
    this.loading = false;
  },
  methods: {
    resetPassword: async function () {
      this.loading = true;
      await axios
        .post(`/api/v1/auth/reset-password`, {
          ...this.form,
          token: this.token,
        })
        .then(({ data }) => (this.done = true))
        .catch((error) => (this.errors = error.response.data.errors));
      this.loading = false;
    },
  },
  components: {
    AuthForm,
    SetPasswordForm,
  },
};
</script>

<style>
</style>