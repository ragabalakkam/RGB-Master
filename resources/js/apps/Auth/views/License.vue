<template>
  <a
    href="https://afaqrgb.com"
    class="d-flex-center bg-light-5 py-2 px-1 border-top font-md"
    :class="`text-${demo ? 'info' : 'success'}`"
    target="_blank"
  >
    <!-- <b-img size="35" :src="`/imgs/brand/favicon.png`" /> -->
    <b-i fas="fad" :icon="demo ? 'desktop-alt' : 'shield-check'" class="fs-5 mr-2" />
    <span class="fs-3" v-t="demo ? 'demoLicenseText' : 'licenseText'" />
  </a>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'license',
  computed: {
    ...mapGetters({
      app: 'app'
    }),
    demo() {
      return this.app.demo || false;
    },
  },
}
</script>