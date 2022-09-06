<template>
  <div class="h-100 d-flex-center">
    <div class="bg-white p-4 min-wd-350" :class="wXS ? 'w-100 h-100' : 'rounded-lg shadow'">
      <div class="text-center py-4" v-if="loading">
        <clip-loader />
      </div>
      <template v-else>
        <div class="text-center">
          <img src="/imgs/register.png" height="200" />
        </div>

        <form class="mb-3" @submit.prevent="register">
          <b-input :label="`${$t('username')} / ${$t('email')} / ${$t('phone')}`" :errors="errors" name="username" v-model="form.username" />
          <b-password-input :label="$t('password')" :errors="errors" v-model="form.password" required />
          <b-checkbox v-model="form.remember">{{ $t('remember') }}</b-checkbox>

          <!-- Forgot password -->
          <div class="my-3">
            <router-link :to="{ name: 'forgot_password' }" v-t="'forgotYourPassword'" />
          </div>

          <b-button variant="primary" type="submit" v-text="$t('login')" :disabled="disabled" block />
        </form>

        <!-- Register -->
        <router-link :to="{ name: 'register' }">
          <span class="py-2" v-t="'or'" />
          <span class="text-success font-bold px-2" v-t="'registerNowForFree'" />
        </router-link>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  name: 'auth.login',
  data() {
    return {
      form: {
        username: null,
        password: null,
        remember: false,
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    disabled() {
      return !this.form.username || !this.form.password || this.form.password.length < 8;
    },
  },
  methods: {
    register: async function () {
      this.loading = true;
      await this.$store.dispatch('auth/login', this.form)
        .then(() => this.$router.push({ name: 'redirect' }))
        .catch(errors => this.errors = errors)
      this.loading = false;
    },
  },
}
</script>