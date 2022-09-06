<template>
  <pop-up v-model="show" class="text-center" :loading="loading" form-class="wd-500">
    <div class="py-4">
      <b-i icon="user-lock" size="3x" class="text-primary" />
      <p class="my-4" v-t="'enterHigherLevelCredentials'" />
      <form @submit.prevent="login" v-show="!loading">      
        <!-- username / email / phone -->
        <b-input
          :errors="errors"
          v-model="form.username"
          input-class="rounded-edges"
          name="username"
          :show-label="false"
          :show-error="false"
          :placeholder="`${$t('email')} ${$t('or')} ${$t('phone')}`"
        />

        <!-- password -->
        <b-password-input
          class="mb-4"
          :errors="errors"
          name="password"
          :show-error="false"
          input-class="rounded-edges"
          :class="{ 'is-invalid': errors.password }"
          v-model="form.password"
          :placeholder="$t('password')"
        />

        <!-- errors -->
        <p
          class="text-danger my-3 font-lg"
          v-if="Object.keys(errors).length"
          v-text="ucFirst($t(`validation.${errors[Object.keys(errors)[0]][0]}`, { attr: $t(Object.keys(errors)[0]) }))"
        />

        <b-button :variant="obj_length(errors) ? 'danger' : 'primary'" type="submit" v-t="'login'" class="rounded-edges px-3" />
      </form>
    </div>
  </pop-up>
</template>

<script>
import PopUp from '../Cashier/views/popups/PopUp';
export default {
  name: 'higher-level-password',
  props: {
    value:  { default: true },
  },
  data() {
    return {
      form: {
        username: null,
        password: null,
      },
      errors: {},
      loading: false,
    };
  },
  computed: {
    show: {
      set(value) {
        this.$emit('input', value);
      },
      get() {
        return this.value;        
      },
    }
  },
  mounted() {
    if (this.$store.getters['app'].demo) {
      this.form = {
        username: "admin",
        password: "passw&rd",
      };
    }
  },
  methods: {
    login: function () {
      this.loading = true;
      this.$store
        .dispatch("cashier/higherLevelLogin", this.form)
        .then(() => this.$emit("proceeded"))
        .catch((errors) => {
          this.errors = errors;
          this.loading = false;
        });
    },
    close: function () {
      this.$emit("closed");
      this.show = false;
    },
  },
  components: { PopUp },
}
</script>