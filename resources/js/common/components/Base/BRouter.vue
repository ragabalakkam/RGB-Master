<template>
  <router-link v-if="enternal" :to="path" @click.native="e => $emit('click', e)">
    <slot />
  </router-link>
  <a v-else :href="path" @click="e => $emit('click', e)">
    <slot />
  </a>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'b-router',
  props: {
    to: { default: null },
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
    path() {
      return this.to || this.relatedRoute();
    },
    enternal() {
      let route = this.$router.match(this.path);
      return route && route.name != '404';
    },
  },
}
</script>