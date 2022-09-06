<template>
  <widget :title="$t('controlX', { attr: $t('navbar') }) + ' (navbar)'">
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
          class="d-flex flex-gap-3 py-3 border-bottom"
          v-for="(link, index) in sorted_links"
          :key="index"
        >
          <div class="d-flex flex-column flex-gap-2">
            <!-- order -->
            <div
              class="text-center wd-40 d-inline-block d-flex flex-column flex-1"
            >
              <b-button
                variant="light"
                size="sm"
                class="flex-1 p-0 d-flex-center mb-1"
                @click="changeOrder(index, -1)"
              >
                <b-i icon="caret-up" />
              </b-button>

              <b-button
                variant="light"
                size="sm"
                class="flex-1 p-0 d-flex-center"
                @click="changeOrder(index, 1)"
              >
                <b-i icon="caret-down" />
              </b-button>
            </div>

            <!-- delete -->
            <div class="wd-40" v-if="wXS">
              <b-button
                variant="outline-danger"
                class="text-hover-light"
                @click="removeLink(index)"
              >
                <b-i icon="trash" />
              </b-button>
            </div>
          </div>

          <div class="flex-1 d-flex flex-column-xs flex-gap-2 flex-gap-md-3">
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
                :dir="links[index].url ? 'ltr' : 'rtl'"
                v-model="links[index].url"
                :placeholder="$t('ال') + $t('url')"
              />
            </div>
          </div>

          <!-- delete -->
          <div class="wd-40" v-if="!wXS">
            <b-button
              variant="outline-danger"
              class="text-hover-light"
              @click="removeLink(index)"
            >
              <b-i icon="trash" />
            </b-button>
          </div>
        </div>

        <!-- Button | add new link  -->
        <b-button
          variant="outline-info"
          class="mt-3 btn-block"
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
const NameInput = () => import("../../../components/Inputs/NameInput.vue");
const Widget = () => import("../../../masters/ControlPanel/components/Widget");
export default {
  name: "manage-navbar-page",
  data() {
    return {
      links: [],
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      navbar_links: "configurations/navbar_links",
      app: "app",
      locales: "locales/locales",
    }),
    sorted_links() {
      return this.links.sort((a, b) => (a.order > b.order ? 1 : -1));
    },
    locales_length() {
      return Object.keys(this.locales).length;
    },
    disabled() {
      return JSON.stringify(this.links) == JSON.stringify(this.navbar_links);
    },
  },
  methods: {
    reset: function () {
      this.links = this.obj_clone(this.navbar_links);
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
        url: `${this.app.url}/`,
      });
    },
    removeLink: function (index) {
      this.links.splice(index, 1);
    },
    update: async function () {
      this.loading = true;
      await this.$store
        .dispatch("configurations/update", {
          key: "navbar_links",
          value: this.links,
        })
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
  },
  watch: {
    navbar_links: {
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