<template>
  <div class="vh-100 d-flex-center flex-column">
    <div class="d-flex-center text-center text-danger flex-column-xs">
      <p
        class="mb-3 f404 font-bolder"
        :class="{ 'border-right border-danger pr-5 mr-5': !wXS }"
      >
        404
      </p>
      <div class="text-left">
        <p class="h1">{{ $t("sorry") }},</p>
        <p class="h3" v-t="'404Page'"></p>
        <p class="mt-5" v-html="$t('404PageMsg')"></p>
      </div>
    </div>

    <b-router
      :to="token ? '/' : '/auth/login'"
      class="btn btn-danger rounded-edges mt-5 px-3"
      v-html="$t('backTo', { attr: $t(token ? 'home' : 'login') })"
    ></b-router>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "not-found-page",
  computed: {
    ...mapGetters({
      token: "auth/token",
    }),
  },
};
</script>

<style>
.f404 {
  font-size: 8rem;
}
</style>