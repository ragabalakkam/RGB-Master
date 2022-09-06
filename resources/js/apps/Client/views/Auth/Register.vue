<template>
  <div class="h-100 d-flex-center">
    <div class="bg-white p-4" :class="wXS ? 'w-100 h-100' : 'rounded-lg shadow'">
      <div class="text-center py-4" v-if="loading">
        <clip-loader />
      </div>
      <template v-else>
        <div class="text-center">
          <img src="/imgs/register.png" height="200" />
        </div>
          
        <form class="mb-3" @submit.prevent="register">
          <b-input :label="$t('ال') + $t('name')" :errors="errors" name="name" v-model="form.name" required />
          <b-input :label="$t('phone')" :errors="errors" name="phone" v-model="form.phone" />
          <b-input :label="$t('email')" :errors="errors" name="email" v-model="form.email" required />
          <set-password-form
            :errors="errors"
            :passwords="{
              password: form.password,
              confirmPassword: form.confirmPassword,
            }"
            v-model="validPasswords"
            @update-password="(val) => (form.password = val)"
            @update-confirm-password="(val) => (form.confirmPassword = val)"
            :name="form.name"
            required
          />
          <b-checkbox class="mt-4" v-model="form.acceptTermsAndConditions">{{ $t('acceptTerms') }}</b-checkbox>
          <b-button variant="success" class="mt-4" type="submit" v-text="$t('register')" :disabled="disabled" block />
        </form>

        <div class="d-flex">
          <p class="flex-1 text-left" v-t="'alreadyHaveAccount'" />
          <router-link class="text-info" :to="{ name: 'login' }" v-t="'login'" />
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import SetPasswordForm from '../../../../common/components/SetPasswordForm.vue';
export default {
  name: 'auth.register',
  data() {return {
      form: {
        name: null,
        phone: null,
        email: null,
        password: null,
        confirmPassword: null,
        acceptTermsAndConditions: false,
      },
      validPasswords: false,
      errors: {},
      loading: false,
    };
  },
  computed: {
    disabled() {
      return !this.form.acceptTermsAndConditions || !this.form.name;
    },
  },
  methods: {
    register: async function () {
      this.loading = true;
      await this.$store.dispatch('auth/register', this.form)
        .then(() => this.$router.push({ name: 'dashboard' }))
        .catch(errors => this.errors = errors)
      this.loading = false;
    },
  },
  components: { SetPasswordForm },
}
</script>