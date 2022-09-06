<template>
  <div class="locales">
    <!-- welcome -->
    <widget v-if="user">
      <h4 class="mb-3" v-text="$t('welcomeX', { attr: user.name.split(' ')[0] })" />
      <span v-t="'locales.note'" />
    </widget>

    <!-- buttons | factory_reset -->
    <div v-if="can('locales.resetDefaults')" class="d-flex">
      <b-button variant="primary" class="shadow d-flex align-items-center" :disabled="loading" @click="factory_reset">
        <span class="font-lg" v-text="ucFirst($t('factory_reset'))" />
        <b-i icon="redo-alt" class="ml-2" v-if="!loading" />
        <pulse-loader v-else size="0.5rem" color="var(--bs-white)" class="ml-2" />
      </b-button>
    </div>

    <div v-show="!loading">
      <!-- locales -->
      <widget>
        <div class="mb-3 pb-3 border-bottom d-flex align-items-center">
          <h4 class="flex-1 text-capitalize" v-text="$t('locales')" />
          <b-button variant="primary" class="px-2 py-1" v-text="$t('createNewX', { attr: $t('locale') }) + $t('ة')" @click="openForm = 'locale'" />
        </div>
        <div class="d-flex flex-column-xs">
          <div class="flex-1 w-sm-100">
            <table class="table w-100 table-borderless locales-container">
              <tbody>
                <tr
                  v-for="(locale, id) in locales"
                  :key="id"
                  class="px-0 border-bottom text-secondary animate bg-hover-light c-ptr"
                  @click="locale_id = id; openForm = 'locale';"
                >
                  <td :class="wXS ? 'clearfix' : 'd-flex'">
                    <img :src="`/imgs/flags/${locale.label}.jpg`" class="ht-20 rounded-flag" />
                  </td>
                  <td v-text="parseName(locale.name)" />
                  <td class="text-uppercase" v-text="locale.label" />
                  <td v-t="locale.dir" />
                </tr>
              </tbody>
            </table>
          </div>
          <div class="d-flex-center ml-md-5 mt-4 mt-sm-0">
            <img src="/imgs/locales.png" class="ht-150" />
          </div>
        </div>
      </widget>

      <!-- translations -->
      <widget class="w-100">
        <div class="mb-3 pb-3 border-bottom d-flex align-items-center">
          <h4
            class="flex-1 text-capitalize"
            v-text="$t('ال') + $t('translations')"
          />
          <b-button
            variant="primary"
            class="px-2 py-1"
            v-text="$t('createNewX', { attr: $t('translation') }) + $t('ة')"
            @click="openForm = 'translation'"
          />
        </div>

        <div v-if="locales">
          <pagination
            class="translations-table"
            v-if="loopedTranslations"
            ref="pagination"
            :items-per-page="5"
          >
            <b-long-x-responsive ref="responsiveX">
              <div class="d-flex flex-gap-bs">
                <div class="flex-1">
                  <p v-text="ucFirst($t('key'))" />
                  <!-- <small>description</small> -->
                </div>
                <div
                  class="flex-1"
                  v-for="locale in locales"
                  :key="locale.id"
                  v-text="ucFirst($t('valueInLocaleX', { attr: $t(locale.label) }))"
                ></div>
              </div>

              <!-- search -->
              <div class="border-bottom py-3 d-flex">
                <b-form-input
                  class="bg-light flex-1"
                  v-model="search.key"
                  :placeholder="$t('searchFor', { attr: $t('key') })"
                />
                <!-- <b-form-input
                  class="bg-light flex-1"
                  v-for="(locale, id) in locales"
                  :key="id"
                  v-model="search[locale.label]"
                  :dir="locale.dir"
                  :disabled="true"
                /> -->
              </div>

              <!-- translations -->
              <template v-if="!loading">
                <pagination-item
                  class="d-flex flex-gap-bs border-bottom animate bg-hover-light c-ptr py-3"
                  v-for="(translation, key) in loopedTranslations"
                  :key="key"
                  @click="translation_key = key; openForm = 'translation';"
                >
                  <div class="flex-1">
                    <p class="font-lg py-1" v-text="key"></p>
                    <!-- <small
                      class="text-secondary text-capitalize"
                      v-text="'some description'"
                    /> -->
                  </div>
                  <div class="flex-1" v-for="(locale, id) in locales" :key="id">
                    <p
                      v-text="translation[locale.label] || $t('undefined')"
                      class="form-control h-auto"
                      :dir="locale.dir"
                    />
                  </div>
                </pagination-item>
              </template>
            </b-long-x-responsive>
          </pagination>

          <!-- search results -->
          <div
            class="font-xl px-3 bg-info text-white rounded-lg d-flex align-items-center"
            v-if="isFiltered && !Object.keys(filtered_translations).length"
          >
            <span
              class="py-2"
              v-text="$t('noMatches', { attr: search.key })"
            ></span>
            ..
            <b-button
              variant="light"
              class="ml-2"
              size="sm"
              v-text="$t('createNewX', { attr: $t('translation') }) + $t('ة')"
              @click="openForm = 'translation'"
            ></b-button>
          </div>
        </div>
      </widget>
    </div>

    <!-- loading -->
    <widget class="d-flex-center" v-show="loading">
      <clip-loader />
    </widget>

    <!-- forms -->
    <b-pop-up class="bring-to-front" v-if="openForm" @close="closeForm">
      <div class="bg-white rounded-xl border shadow wd-sm-450 py-5 px-4 px-md-5" :class="{'w-100' : this.wXS}">
        <create-locale
          v-if="openForm === 'locale'"
          :id="locale_id"
          @close="closeForm"
        />
        <create-translation
          v-if="openForm === 'translation'"
          :translation_key="translation_key"
          @close="closeForm"
        />
      </div>
    </b-pop-up>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
const Pagination = () => import("../../../../common/vendor/Pagination/Pagination");
const PaginationItem = () => import("../../../../common/vendor/Pagination/PaginationItem");
const CreateLocale = () => import("./CreateLocale");
const createTranslation = () => import("./createTranslation");
export default {
  name: "locales",
  computed: {
    ...mapGetters({
      user: "auth/user",
      locales: "locales/locales",
      translations: "locales/translations",
    }),
    isFiltered() {
      return this.search.key.length;
    },
    loopedTranslations() {
      return this.isFiltered ? this.filtered_translations : this.translations;
    },
    count() {
      return this.isFiltered
        ? Object.keys(this.filtered_translations).length
        : this.limit.current;
    },
  },
  data() {
    return {
      loading: true,
      search: { key: "" },
      filtered_translations: {},
      limit: {
        max: 5,
        step: 5,
        current: 5,
      },
      openForm: null,
      translation_key: null,
      locale_id: null,
    };
  },
  methods: {
    factory_reset: function () {
      this.loading = true;
      this.$store
        .dispatch("locales/factory_reset")
        .then(() => (this.loading = false));
    },
    closeForm: function () {
      this.locale_id = null;
      this.translation_key = null;
      this.searchTranslations();
      this.openForm = null;
    },
    searchTranslations: async function () {
      this.loading = true;
      let results = {};
      await Object.keys(this.translations)
        .filter((key) =>
          key.toLowerCase().includes(this.search.key.toLowerCase())
        )
        .forEach((key) => (results[key] = this.translations[key]));
      this.filtered_translations = results;
      await setTimeout(() => {
        this.$refs.pagination.refresh();
        this.$refs.responsiveX.refresh();
      }, 50);
      this.loading = false;
    },
  },
  mounted() {
    this.$store.dispatch("locales/fetchAll").then(() => {
      Object.values(this.locales).forEach((locale) =>
        Vue.set(this.search, locale.label, "")
      );
      this.limit.max = Object.keys(this.translations).length;
      this.loading = false;
    });
  },
  watch: {
    "search.key": {
      handler: "searchTranslations",
      deep: true,
    },
  },
  components: {
    Widget,
    CreateLocale,
    createTranslation,
    Pagination,
    PaginationItem,
  },
};
</script>

<style lang="scss" scoped>
.bring-to-front {
  z-index: 999;
}

.locales-container {
  td:nth-child(1) {
    width: 20%;
  }
  td:nth-child(2) {
    width: 25%;
  }
  td:nth-child(3) {
    width: 15%;
  }
  td:nth-child(4) {
    width: 40%;
  }
}

.animate:hover {
  padding-left: 1rem;
  padding-right: 1rem;
  transition: var(--duration-fast) ease-in-out;
}
</style>