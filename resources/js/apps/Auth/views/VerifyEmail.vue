<template>
  <div class="w-100 h-100 d-flex-center">
    <auth-form class="rounded-xl">
      <template v-slot:form>
        <!-- loading -->
        <clip-loader v-if="loading" />

        <!-- verified successfully -->
        <div class="text-success py-5" v-else-if="done">
          <b-i icon="check-circle" size="4x" class="d-block mb-4"></b-i>
          <p class="h3 mb-2" v-html="$t('verifyEmail.success')"></p>
        </div>

        <!-- invalid -->
        <div class="text-dark text-center py-5" v-else-if="error === 'invalid'">
          <b-i icon="times-circle" size="4x" class="d-block mb-4"></b-i>
          <p class="h3 px-3" v-html="$t(`verifyEmail.errors.${error}`)"></p>
        </div>

        <!-- already verified -->
        <div
          class="text-secondary text-center mb-3 py-5"
          v-else-if="error === 'verified'"
        >
          <b-i icon="check-circle" size="4x" class="d-block mb-4"></b-i>
          <p class="h3 px-3" v-html="$t(`verifyEmail.errors.${error}`)"></p>
        </div>

        <!-- already verified -->
        <div class="text-secondary text-center py-5" v-else>
          <b-i icon="times-circle" size="4x" class="d-block mb-4"></b-i>
          <p class="h3 px-3" v-html="$t('somethingWentWrong')"></p>
        </div>
      </template>
      <template v-slot:footer>
        <b-router
          :to="'/'"
          class="d-block text-primary"
          v-html="$t('backTo', { attr: $t(token ? 'home' : 'login') })"
        ></b-router>
      </template>
    </auth-form>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const AuthForm = () => import("../../../common/masters/Auth/AuthForm.vue");
export default {
  name: "verify-email",
  computed: {
    ...mapGetters({
      token: "auth/token",
    }),
  },
  data() {
    return {
      loading: false,
      error: null,
      done: false,
    };
  },
  async mounted() {
    this.loading = true;
    await this.$store
      .dispatch("auth/verifyEmail", this.$route.params.token)
      .then((msg) => (this.done = true))
      .catch((error) => (this.error = error.token[0]));
    this.loading = false;
  },
  components: {
    AuthForm,
  },
};
</script>

<style>
</style>