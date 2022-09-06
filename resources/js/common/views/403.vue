<template>
  <div class="vh-100 d-flex-center flex-column">
    <clip-loader v-if="loading" />

    <template v-else>
      <div class="d-flex-center text-center text-secondary flex-column-xs">
        <p class="mb-3 f403 font-bolder" :class="{ 'border-right border-secondary pr-5 mr-5': !wXS }">
          403
        </p>
        <div class="text-left">
          <p class="h3" v-t="'403Page'"></p>
          <p class="mt-5" v-html="$t('403PageMsg')"></p>
        </div>
      </div>

      <b-router
        :to="token ? '/redirect' : '/auth/login'"
        class="btn btn-secondary rounded-edges mt-5 px-3"
        v-html="$t('backTo', { attr: $t(token ? 'home' : 'login') })"
      />
    </template>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "not-authorized-page",
  data() {
    return {
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      token: "auth/token",
      routes_permissions: 'permissions/routes_permissions',
    }),
  },
  mounted: async function() {

    if (!this.token)
      return this.loading = false;

    try {
      await this.$store.dispatch('auth/fetchPermissions')
        .then(user_perms => {
          let name = this.$route.params.route,
            needed = this.routes_permissions[name] || null;

          if (!needed || !needed.length || needed.filter(p => user_perms.includes(p)).length)
            this.$router.push({ name, params: { action: 'create' } });
        
          else this.loading = false;
        });
    } catch (e) {}

    setTimeout(() => this.loading = false, 1000);
  },
};
</script>

<style lang="scss" scoped>
.f403 {
  font-size: 8rem;
}
</style>