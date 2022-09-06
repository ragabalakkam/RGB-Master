<template>
  <div class="d-flex">
    <div class="flex-1 d-flex flex-gap-2 text-secondary-8">
      <div
        v-for="locale in locales"
        :key="locale.id"
        class="c-ptr"
        :class="{ 'text-primary' : getLocale() == locale.label }"
        @click="$store.dispatch('locales/change', { label: locale.label })"
      >
        {{ parseName(locale.name) }}
      </div>
    </div>

    <div class="d-flex">
      <a
        v-for="(link, index) in links"
        :key="index"
        :href="link.url"
        target="_blank"
        class="d-block text-info text-hover-primary px-1 ml-2"
      >
        <b-i
          :icon="icons[link.name.en] || icons['default']"
          :fas="icons[link.name.en] ? 'fab' : 'fal'"
        />
      </a>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "social-media-bar",
  computed: {
    ...mapGetters({
      social_media_links: "configurations/social_media_links",
      icons: "icons",
      locales: 'locales/locales',
    }),
    links() {
      return this.social_media_links
        .sort((a, b) => (a.order > b.order ? 1 : -1))
        .reverse();
    },
  },
};
</script>

<style>
</style>