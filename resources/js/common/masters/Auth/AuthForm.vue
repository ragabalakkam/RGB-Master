<template>
  <div class="auth-form bg-white text-center p-5 d-flex flex-column position-relative shadow-lg">
    <div class="flex-1 d-flex flex-column pt-4">
      <b-img v-if="app" :src="`/storage/${info.logo}`" size="75" alt="logo" class="container-fluid mb-2" />
      <p v-if="app" class="h1 text-primary mb-2" v-text="parseName(info.name)" />
      <p class="text-capitalize my-2" v-t="$route.name.replace('-', ' ')" />

      <!-- form slot -->
      <div class="flex-1 d-flex-center">
        <div class="w-100 mt-3">
          <slot name="form" />
        </div>
      </div>

      <!-- form footer slot (if exists) -->
      <template v-if="$slots['footer']">
        <div><hr class="m4-3 mb-3" /></div>
        <slot name="footer" />
      </template>

      <!-- license -->
      <license style="margin: 2rem -3rem -3rem -3rem;" />

      <!-- languages menu -->
      <div class="dropdown d-flex-center position-absolute position-top">
        <b-button
          class="d-flex-center bg-all-none flex-gap-2 rounded-rop-0 border-0"
          id="languages-dropdown-menu"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <b-i icon="angle-down" class="fs-3 text-secondary-5" />
          <img :src="`/imgs/flags/${getLocale()}.jpg`" class="wd-25 rounded-flag" />
        </b-button>
        <languages-menu aria-labelledby="languages-dropdown-menu" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const LanguagesMenu = () => import("../ControlPanel/Menus/LanguagesMenu.vue")
const License = () => import('../../../apps/Auth/views/License.vue')
export default {
  name: "auth-form",
  computed: {
    ...mapGetters({
      app: "app",
      info: "configurations/app",
    }),
  },
  components: {
    LanguagesMenu,
    License,
  },
};
</script>

<style lang="scss" scoped>
.auth-form {
  width: 21.875rem;
  overflow: hidden;
  text-align: center;

  .appName {
    font-size: 3rem;
  }

  input[type="checkbox"] {
    + label {
      font-weight: 600;
    }
  }

  input[type="checkbox"]:not(:checked) {
    + label {
      color: lightgrey;
    }
  }
}

.error {
  height: 2rem;
  line-height: 2rem;
  margin-left: -3rem;
  margin-right: -3rem;
}
</style>