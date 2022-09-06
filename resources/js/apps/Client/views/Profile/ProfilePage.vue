<template>
  <client-page v-model="loading" v-if="user">
    <form class="row" @submit.prevent="submit" @reset.prevent="reset">
      <div class="col-12 mb-4">
        <b-img-input :src="img(user.image)" v-model="avatar" class="rounded-circle mx-auto mb-2 sz-180" id="change-pp" />
        <b-error :field="errors.image" attr="image" />
        <label
          class="d-block mb-4 text-primary text-hover-dark c-ptr text-center"
          for="change-pp"
          v-t="'change profile picture'"
        />
      </div>
      
      <!-- name -->
      <b-input class="col-md-6" :label="$t('ال') + $t('name')" v-model="form.name" name="name" :errors="errors" :disabled="!editing" />

      <!-- username -->
      <b-input class="col-md-6" :label="$t('username')" v-model="form.username" name="username" :errors="errors" :disabled="!editing" />

      <!-- email -->
      <b-input class="col-md-6" :label="$t('email')" v-model="form.email" name="email" type="email" step="1" :errors="errors" :disabled="!editing" />

      <!-- phone -->
      <b-input class="col-md-6" input-class="no-spinners" v-model="form.phone" name="phone" type="number" step="1" :label="$t('phone')" :errors="errors" :disabled="!editing" />
      
      <div class="col-12"><hr></div>

      <!-- address -->
      <address-input class="col-12 mb-4" :label="$t('ال') + $t('name')" v-model="form.address" name="name" :errors="errors" :disabled="!editing" />

      <!-- submit / start editing -->
      <div class="col-12 t-3 text-center">
        <div class="d-flex flex-gap-2 align-items-center w-max-content mx-auto" v-if="editing">
          <b-button variant="primary" type="submit" class="d-flex-center" :disabled="disabled" v-t="'update'" />
          <b-button variant="light" type="reset" v-t="'cancel'" />
        </div>
        <b-button v-else variant="success" v-text="$t('editX', { attr: $t('personalInfo') })" class="rounded-edges" type="button" @click="editing = true" />
      </div>
    </form>
  </client-page>
</template>

<script>
import { mapGetters } from "vuex";
const ClientPage  = () => import('../../../../common/masters/Client/ClientPage.vue');
const AddressInput = () => import('../../../../common/components/Inputs/AddressInput.vue');
const SetPasswordForm = () => import('../../../../common/components/SetPasswordForm.vue');
export default {
  name: "profile-page",
  data() {
    return {
      form: {
        name: null,
        email: null,
        phone: null,
        username: null,
        address: [null, null, null, null, null, null, null],
      },
      avatar: null,
      errors: {},
      loading: false,
      editing: false,
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
    }),
    disabled() {
      if (this.loading) 
        return true;

      let disabled = true;
      Object.keys(this.form).forEach((key) => {
        if (this.user[key] !== this.form[key])
          disabled = false;
      });

      return disabled;
    },
  },
  mounted() {
    this.reset();
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store
        .dispatch("auth/editProfile", this.form)
        .then(() => {
          this.editing = false;
          this.errors = {};
        })
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    reset: async function () {
      this.loading = true;
      Object.keys(this.form).forEach(key => (this.form[key] = this.user[key]));
      this.editing = false;
      this.loading = false;
    },
    // 
    changeProfilePicture: async function () {
      this.$store
        .dispatch("auth/changeProfilePicture", this.avatar)
        .catch((errors) => (this.errors = errors));
    },
  },
  watch: {
    avatar: function (newVal) {
      if (newVal) this.changeProfilePicture();
    },
  },
  components: {
    ClientPage,
    AddressInput,
    SetPasswordForm,
  },
};
</script>