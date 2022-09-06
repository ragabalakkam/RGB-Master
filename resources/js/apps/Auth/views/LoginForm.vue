<template>
  <auth-form class="login-form">
    <template v-slot:form>
      <!-- loading -->
      <clip-loader v-if="loading" />

      <!-- login form -->
      <template v-else>
        <!-- errors -->
        <p
          class="text-danger mb-3 font-lg text-capitalize"
          v-if="Object.keys(errors).length"
          v-text="$t(`validation.${errors[Object.keys(errors)[0]][0]}`, { attr: $t(Object.keys(errors)[0]), })"
        />

        <form @submit.prevent="login" class="w-100">
          <!-- username / email / phone -->
          <b-input
            :errors="errors"
            v-model="form.username"
            input-class="rounded-edges"
            name="username"
            :show-label="false"
            :show-error="false"
            :placeholder="`${$t('email')} ${$t('or')} ${$t('phone')}`"
          />

          <!-- password -->
          <b-password-input
            input-class="rounded-edges"
            :class="{ 'is-invalid': errors.password }"
            v-model="form.password"
            :errors="errors"
            :placeholder="$t('password')"
            :showError="false"
          />

          <!-- remember me -->
          <b-form-group class="mx-auto my-3 font-bolder" :class="`text-${form.remember ? 'primary' : 'secondary-4'}`">
            <input type="checkbox" id="remember" class="d-none" v-model="form.remember" />
            <label for="remember" class="c-ptr no-select d-flex-center">
              <b-i :icon="`${form.remember ? 'check-' : ''}circle`" />
              <span class="ml-1" v-t="'remember'" />
            </label>
          </b-form-group>

          <!-- submit button -->
          <b-button
            variant="primary"
            type="submit"
            class="rounded-edges my-3 text-uppercase btn-block"
            :disabled="!form.username || !form.password || form.password.length < 8"
            v-t="'login'"
          />
        </form>

        <!-- Forgot password -->
        <router-link
          class="d-block mt-2"
          :to="{ name: 'forgot-password' }"
          v-t="'forgotYourPassword'"
        />
      </template>
    </template>

    <!-- register -->
    <template v-slot:footer v-if="app.demo">
      <router-link :to="{ name: 'register' }">
        <span class="py-2 mr-1" v-t="'or'" />
        <span class="text-success font-bold px-2" v-t="'registerNowForFree'" />
      </router-link>
    </template>
  </auth-form>
</template>

<script>
import { mapGetters } from 'vuex';
const AuthForm = () => import("../../../common/masters/Auth/AuthForm");
export default {
  name: "login-form",
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
    ...mapGetters({
      app: 'app',
    }),
  },
  mounted() {
    if (this.app.demo) {
      this.form = {
        username: "cashier",
        password: "passw&rd",
        remember: true,
      };
    }
  },
  methods: {
    login: function () {
      this.loading = true;
      this.$store
        .dispatch("auth/login", this.form)
        .then(() => this.$emit("proceeded"))
        .catch((errors) => {
          this.errors = errors;
          this.loading = false;
        });
    },
  },
  components: {
    AuthForm,
  },
};
</script>