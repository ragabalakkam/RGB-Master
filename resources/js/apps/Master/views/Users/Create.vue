<template>
  <div>
    <create-page
      @submit="$router.push({ name: 'employees.index' })"
      @reset="$router.push({ name: 'employees.index' })"
      @errors="(err) => (errors = err)"
      :errors="errors"
      :on-created-actions="['employees/index']"
      :title="createOrUpdate('user')"
      module="employees"
      v-model="form"
      permission="employees"
      :disabled="!form.name || (_action == 'create' && !validPasswords)"
    >
      <!-- name -->
      <b-input class="col-12" :label="$t('ال') + $t('name')" name="name" attr="name" :errors="errors" v-model="form.name" required />

      <!-- phone -->
      <phone-input class="col-md-6" v-model="form.phone" :errors="errors" :label="$t('phone')" />

      <!-- email -->
      <b-input class="col-md-6" type="email" name="email" attr="email" :errors="errors" v-model="form.email" />

      <!-- change password -->
      <div class="col-12" v-if="canChangePassword">
        <hr />

        <set-password-form
          v-if="_action == 'create'"
          :errors="errors"
          :passwords="{ password: form.password, confirmPassword: form.confirmPassword }"
          :name="form.name"
          v-model="validPasswords"
          @update-password="(val) => (form.password = val)"
          @update-confirm-password="(val) => (form.confirmPassword = val)"
        />

        <div v-else>
          <b-button variant="info" class="border d-flex-center flex-gap-2 py-2" @click="openPasswordForm = true">
            {{ $t('resetPassword') }}
            <b-i icon="user-lock" />
          </b-button>
        </div>
      </div>

      <!-- roles -->
      <div v-if="canModifyRoles" class="col-12">
        <hr />
        <b-form-group :label="$t('ال') + $t('permissions')" label-class="fs-3 mb-2">
          <roles-input v-model="form.role_ids" />
        </b-form-group>
        <hr />
      </div>
    </create-page>
    
    <floating-form v-if="canChangePassword && _action == 'update' && openPasswordForm">
      <widget class="mx-auto p-5" :class="{ 'wd-350' : !wXS }" v-if="!passwordSetSuccessfully" v-model="passwordFormLoading">
        <form @submit.prevent="changePassword" @reset.prevent="openPasswordForm = false">
          <!-- <b-password-input
            class="mb-3"
            :label="$t('oldX', { x: $t('password')}) + $t('ة')"
            :placeholder="$t('oldX', { x: $t('password')}) + $t('ة')"
            :errors="errors"
            name="oldPassword"
            v-model="form.oldPassword"
          /> -->
          <set-password-form
            class="mb-5"
            :errors="errors"
            :passwords="{ password: form.password, confirmPassword: form.confirmPassword }"
            :name="form.name"
            v-model="validPasswords"
            @update-password="(val) => (form.password = val)"
            @update-confirm-password="(val) => (form.confirmPassword = val)"
          />
          <div class="d-flex flex-gap-2 align-items-center w-max-content mx-auto">
            <b-button variant="primary" type="submit" class="d-flex-center" :disabled="/*!form.oldPassword ||*/ !validPasswords" v-t="'update'" />
            <b-button variant="light" type="reset" v-t="'cancel'" />
          </div>
        </form>
      </widget>
      <div class="bg-white rounded-lg p-4 wd-280 text-center text-primary mx-auto" v-else>
        <b-i icon="check-circle" size="5x" class="d-block mb-3" />
        <p class="fs-6" v-html="$t('passwordHasBeenReset')" />
      </div>
    </floating-form>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const CreatePage = () => import("../../../../common/masters/ControlPanel/pages/CreatePage");
import FloatingForm from '../../../../common/components/FloatingForm.vue';
import PhoneInput from '../../../../common/components/Inputs/PhoneInput.vue';
import RolesInput from '../../../../common/components/Inputs/RolesInput.vue';
import SetPasswordForm from '../../../../common/components/SetPasswordForm.vue';
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
export default {
  name: "employees.create",
  props: {
    id    : { default : null },
    action: { default : null },
  },
  data() {
    return {
      form: {
        name: null,
        phone: null,
        email: null,
        role_ids: [],

        // old_password: null,
        password: null,
        confirmPassword: null,
      },
      errors: {},
      validPasswords: false,
      passwordSetSuccessfully: false,
      openPasswordForm: false,
      loading: false,
      passwordFormLoading: false,
    };
  },
  computed: {
    ...mapGetters({
      employees : "employees/employees",
    }),
    _id() {
      return this.id || this.$route.params.id;
    },
    _action() {
      return this.action || this.$route.params.action || 'create';
    },
    canChangePassword() {
      return this.can('employees.change_password');
    },
    canModifyRoles() {
      return this.can('employees.modify_roles');
    },
  },
  methods: {
    changePassword: async function () {
      this.passwordFormLoading = true;
      await this.$store.dispatch('employees/changePassword', {
        id                  : this.form.id,
        // oldPassword         : this.form.oldPassword,
        newPassword         : this.form.password,
        confirmNewPassword  : this.form.confirmPassword
      })
        .then(() => {
          this.passwordSetSuccessfully = true;
          setTimeout(() => {
            this.openPasswordForm = false;
            this.passwordSetSuccessfully = false;
          }, 1500);
        })
        .catch(err => this.errors = err);
      this.passwordFormLoading = false;
    },
  },
  components: {
    PhoneInput,
    SetPasswordForm,
    RolesInput,
    Widget,
    FloatingForm,
    CreatePage
  },
};
</script>