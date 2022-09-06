<template>
  <form class="text-left">
    <!-- Password -->
    <div class="form-group">
      <b-password-input
        :label="passwordLabel || $t('password')"
        class="font-lg"
        :input-class="errors.password ? 'is-invalid' : ''"
        :placeholder="$t('password')"
        v-model="password"
        @enter="$emit('enter')"
      />
      <b-error v-if="errors" :field="errors.password" :attrs="{ min: 8 }" attr="password" />
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
      <b-password-input
        :label="confirmPasswordLabel || $t('confirmPassword')"
        class="font-lg"
        :input-class="errors.confirmPassword ? 'is-invalid' : ''"
        :placeholder="$t('confirmPassword')"
        v-model="confirmPassword"
        @enter="$emit('enter')"
        :required="required"
      />
      <b-error v-if="errors" :field="errors.confirmPassword" :attrs="{ attr2: $t('password') }" attr="confirmPassword" />
    </div>

    <!-- Validations -->
    <div tabindex="-1" class="text-secondary-5 text-left pt-2 font-md">
      <p class="mb-2" :class="{ 'text-dark': passwordValidations.notContainName }">
        <b-i :icon="passwordValidations.notContainName ? 'check pr-1' : 'times pr-2'" />
        {{ $t("setPassword.notIncludingYourName") }}
      </p>
      <p class="mb-2" :class="{ 'text-dark': passwordValidations.includesSpecial }">
        <b-i :icon="passwordValidations.includesSpecial ? 'check pr-1' : 'times pr-2'" />
        {{ $t("setPassword.includesSpeicalCharacters") }}
      </p>
      <p class="mb-2" :class="{ 'text-dark': passwordValidations.min }">
        <b-i :icon="passwordValidations.min ? 'check pr-1' : 'times pr-2'" />
        {{ $t("setPassword.includesAtLeastCharacters") }}
      </p>
      <p :class="{ 'text-dark': passwordValidations.match }">
        <b-i :icon="passwordValidations.match ? 'check pr-1' : 'times pr-2'" />
        {{ $t("setPassword.passwordsMustMatch") }}
      </p>
    </div>
  </form>
</template>

<script>
export default {
  name: "set-password-form",
  props: {
    value               : { require: true, type: Boolean },
    passwords           : { required: true, type: Object },
    name                : { default: null, type: String },
    errors              : { required: true, type: Object },
    labelsClass         : { default: "text-capitalize" },
    passwordLabel       : { default: null },
    confirmPasswordLabel: { default: null },
    required            : { default: false },
  },
  data() {
    return {
      passwordValidations: {
        notContainName: false,
        includesSpecial: false,
        min: false,
        match: false,
      },
    };
  },
  computed: {
    isValid: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    password: {
      set(val) {
        this.$emit("update-password", val);
      },
      get() {
        return this.passwords.password || "";
      },
    },
    confirmPassword: {
      set(val) {
        this.$emit("update-confirm-password", val);
      },
      get() {
        return this.passwords.confirmPassword || "";
      },
    },
    _id() {
      return Math.ceil(Math.random() * 1651651651);
    },
  },
  methods: {
    validatePasswords: function () {
      // min 8
      this.passwordValidations.min = this.password.length >= 8;

      // containing special characters
      this.passwordValidations.includesSpecial = !!this.password.match(/(?=.*[!@#$%^&*])/);

      // not contining name
      if (this.name) {
        let names = this.name.split(" "), isNameIncludedLength = 0;
        names.forEach((name) => {
          if (this.password.indexOf(name.toLowerCase()) === -1)
            isNameIncludedLength++;
        });
        this.passwordValidations.notContainName = isNameIncludedLength === names.length;
      } else this.passwordValidations.notContainName = true;

      // passwords match
      this.passwordValidations.match = this.password.length && this.password === this.confirmPassword;

      this.isValid =  Object.values(this.passwordValidations).filter((check) => check)
                            .length === Object.keys(this.passwordValidations).length;
    },
  },
  watch: {
    password: {
      handler: "validatePasswords",
      immediate: true,
    },
    confirmPassword: {
      handler: "validatePasswords",
      immediate: true,
    },
  },
};
</script>