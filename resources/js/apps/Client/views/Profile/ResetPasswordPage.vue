<template>
  <client-page v-model="loading" v-if="user">

    <!-- errors -->
    <p
      v-if="!passwordSetSuccessfully && Object.keys(errors).length"
      class="text-danger mb-3 font-lg text-capitalize"
      v-text="$t(`validation.${errors[Object.keys(errors)[0]][0]}`, { attr: $t(Object.keys(errors)[0]), })"
    />
  
    <!-- form -->
    <form v-if="!passwordSetSuccessfully" @submit.prevent="submit" @reset.prevent="reset">
      <b-password-input
        class="mb-3"
        :label="$t('oldX', { x: $t('password')}) + $t('ة')"
        :placeholder="$t('oldX', { x: $t('password')}) + $t('ة')"
        :errors="errors"
        name="oldPassword"
        v-model="form.oldPassword"
      />
      <set-password-form
        class="mb-4"
        :errors="errors"
        :passwords="{ password: form.newPassword, confirmPassword: form.confirmNewPassword }"
        :name="form.name"
        v-model="validPasswords"
        @update-password="(val) => (form.newPassword = val)"
        @update-confirm-password="(val) => (form.confirmNewPassword = val)"
      />
      <div class="d-flex flex-gap-2 align-items-center">
        <b-button variant="primary" type="submit" class="d-flex-center" :disabled="!form.oldPassword || !validPasswords" v-t="'update'" />
        <b-button variant="light" type="reset" v-t="'cancel'" @click="tab = 0" />
      </div>
    </form>

    <div class="d-flex-center ht-400" v-else>
      <div class="text-center text-primary">
        <b-i icon="check-circle" size="5x" class="d-block mb-3" />
        <p class="mb-4 fs-6" v-html="$t('passwordHasBeenReset')" />
      </div>
    </div>
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
        oldPassword: null,
        newPassword: null,
        confirmNewPassword: null,
      },
      errors: {},
      validPasswords: false,
      passwordSetSuccessfully: false,
      loading: false,
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
  mounted: function () {
    this.reset();
  },
  methods: {
    submit: function () {
      this.$store.dispatch('auth/changePassword', this.form)
        .then(data => this.passwordSetSuccessfully = true)
        .catch(err => this.errors = err);
    },
    reset: function () {
      this.form = { oldPassword: null, newPassword: null, confirmNewPassword: null };
    },
  },
  components: {
    ClientPage,
    AddressInput,
    SetPasswordForm,
  },
};
</script>