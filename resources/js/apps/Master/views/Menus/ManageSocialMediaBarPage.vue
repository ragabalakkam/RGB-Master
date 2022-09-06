<template>
  <widget :title="$t('controlX', { attr: $t('social_media_bar') })">
    <b-form
      action="update"
      :loading="loading"
      :disabled="disabled"
      @submit="update"
      @reset="reset"
    >
      <div class="col-12">
        <!-- head -->
        <div class="d-flex flex-gap-bs" v-if="!wXS">
          <p class="wd-40" v-text="$t('ال') + $t('ordering')" />
          <p class="wd-40" v-text="$t('ال') + $t('icon')" />
          <div :class="`flex-${locales_length} d-flex flex-gap-bs`">
            <p
              class="flex-1"
              v-for="locale in locales"
              :key="locale.id"
              v-text="$t('nameInLocaleX', { attr: $t(locale.label) })"
            />
          </div>
          <p class="flex-1" v-text="$t('ال') + $t('url')" />
          <p class="wd-40" v-t="'delete'" />
        </div>

        <!-- rows -->
        <div
          class="d-flex flex-gap-3 flex-gap-md-bs py-3 border-bottom"
          v-for="(link, index) in sorted_links"
          :key="index"
        >
          <!-- order -->
          <div
            class="text-center d-flex flex-column flex-gap-2 flex-gap-md-bs wd-40"
          >
            <!-- icon -->
            <div class="wd-40" v-if="wXS">
              <div class="btn btn-light d-flex-center font-xl py-2">
                <b-i
                  :icon="icons[links[index].name.en] || icons['default']"
                  :fas="icons[links[index].name.en] ? 'fab' : 'fal'"
                />
              </div>
            </div>

            <div class="flex-1 d-flex flex-column flex-gap-1">
              <b-button
                variant="light"
                class="flex-1 p-0 d-flex-center"
                @click="changeOrder(index, -1)"
              >
                <b-i icon="caret-up" />
              </b-button>
              <b-button
                variant="light"
                class="flex-1 p-0 d-flex-center"
                @click="changeOrder(index, 1)"
              >
                <b-i icon="caret-down" />
              </b-button>
            </div>

            <!-- delete button -->
            <b-button
              v-if="wXS"
              variant="outline-danger"
              @click="removeLink(index)"
            >
              <b-i icon="trash" />
            </b-button>
          </div>

          <div class="flex-1 d-flex flex-column-xs flex-gap-2 flex-gap-md-bs">
            <!-- icon -->
            <div class="wd-40" v-if="!wXS">
              <div class="btn btn-light d-flex-center font-xl py-2">
                <b-i
                  :icon="icons[links[index].name.en] || icons['default']"
                  :fas="icons[links[index].name.en] ? 'fab' : 'fal'"
                />
              </div>
            </div>

            <!-- name -->
            <div :class="`flex-${locales_length}`">
              <name-input
                class="mb-0"
                v-model="links[index].name"
                :errors="{}"
                :showLabels="false"
                :gap="wXS ? 2 : 4"
              />
            </div>

            <!-- url -->
            <div class="flex-1">
              <b-form-input
                dir="ltr"
                v-model="links[index].url"
                :placeholder="$t('ال') + $t('url')"
              />
            </div>
          </div>

          <!-- delete button -->
          <b-button
            v-if="!wXS"
            variant="outline-danger"
            class="text-hover-light"
            @click="removeLink(index)"
          >
            <b-i icon="trash" />
          </b-button>
        </div>

        <!-- Button | add new link  -->
        <b-button
          variant="outline-info"
          block
          v-text="$t('createNewX', { attr: $t('link') })"
          @click="addLink"
          v-if="
            links[links.length - 1].name.en &&
            links[links.length - 1].name.ar &&
            links[links.length - 1].url
          "
        />
      </div>
    </b-form>
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../masters/ControlPanel/components/Widget");
const NameInput = () => import("../../../components/Inputs/NameInput.vue");
export default {
  name: "manage-social-media-page",
  data() {
    return {
      links: [],
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      social_media_links: "configurations/social_media_links",
      app: "app",
      icons: "icons",
      locales: "locales/locales",
    }),
    sorted_links() {
      return this.links.sort((a, b) => (a.order > b.order ? 1 : -1));
    },
    locales_length() {
      return Object.keys(this.locales).length;
    },
    disabled() {
      return (
        JSON.stringify(this.links) == JSON.stringify(this.social_media_links)
      );
    },
  },
  methods: {
    reset: function () {
      this.links = this.obj_clone(this.social_media_links);
    },
    changeOrder: function (index, change) {
      let newVal = parseInt(this.links[index].order) + change;
      if (newVal > 0 && newVal < this.links.length + 1) {
        this.links[newVal - 1].order = this.links[index].order;
        this.links[index].order = newVal;
      }
    },
    addLink: function () {
      this.links.push({
        name: {},
        order: parseInt(this.links[this.links.length - 1].order) + 1,
        url: `https://`,
      });
    },
    removeLink: function (index) {
      this.links.splice(index, 1);
    },
    update: async function () {
      this.loading = true;
      await this.$store
        .dispatch("configurations/update", {
          key: "social_media_links",
          value: this.links,
        })
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
  },
  watch: {
    social_media_links: {
      handler: "reset",
      immediate: true,
    },
  },
  components: {
    Widget,
    NameInput,
  },
};
</script>

<style lang="scss" scoped>
</style>