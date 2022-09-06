<template>
  <loading />
</template>

<script>
import { mapGetters } from 'vuex'
const Loading = () => import("../components/Loading.vue");
export default {
  name: 'redirect',
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
  },
  mounted() {
    return this.smartRedirect(this.relatedRoute());
  },
  components: {
    Loading,
  },
}
</script>