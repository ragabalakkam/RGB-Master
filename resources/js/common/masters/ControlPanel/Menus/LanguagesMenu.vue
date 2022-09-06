<template>
  <b-menu class="languages-menu wd-150 c-ptr">
    <template v-slot:body>
      <div
        v-for="(locale, index) in all_locales_but_selected"
        :key="index"
        class="px-3 py-2 border-bottom border-light d-flex align-items-center bg-hover-light"
        @click="switchLocale(locale.label)"
      >
        <img
          :src="`/imgs/flags/${locale.label}.jpg`"
          class="wd-25 mr-3 rounded-flag"
        />
        <span class="text-capitalize" v-text="parseName(locale.name)" />
      </div>
    </template>
  </b-menu>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "languages-menu",
  computed: {
    ...mapGetters({
      locales: "locales/locales",
    }),
    locale() {
      return this.getLocale();
    },
    all_locales_but_selected() {
      return Object.values(this.locales).filter((l) => l.label !== this.locale);
    },
  },
  methods: {
    switchLocale: function (label) {
      this.$store.dispatch("locales/change", { label });
    },
  },
};
</script>