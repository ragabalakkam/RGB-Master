<template>
  <div class="footage bg-white text-dark text-center fs-3">
    <div class="container py-1 d-flex-center">
      <span v-text="$t('copyright', { appName: parseName(app.name) })"></span>
      <b-i fas="fal" icon="copyright" class="fs-2 mb-1 ml-1" />
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "footage",
  computed: {
    ...mapGetters({
      app: "configurations/app",
    }),
  },
};
</script>