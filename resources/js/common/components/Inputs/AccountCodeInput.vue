<template>
  <b-form-group :label="label || $t('ال') + $t('code')">
    <div
      class="form-control d-flex py-0 flex-gap-2"
      :class="[{ 'is-invalid': errors.code }, { 'border-primary': isFocused }]"
    >
      <label
        v-if="parentCode"
        for="account-code-id"
        class="d-inline-block w-max-content form-control h-100 border-0 px-0 text-primary font-bolder no-select"
        :class="{ 'order-2 flex-1': isAr }"
        v-text="parentCode"
      />

      <b-input
        v-model="code"
        id="account-code-id"
        type="number"
        step="1"
        min="1"
        :class="{ 'flex-1': !isAr }"
        class="wd-80"
        input-class="border-0 h-100 px-0"
        @focus="isFocused = true"
        @blur="isFocused = false"
      />
    </div>
    <b-error :field="errors.code" attr="code" class="mb-3 mt-1" />
  </b-form-group>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "account-code-input",
  props: {
    value: { required: true },
    parentAccountId: { default: null },
    label: { default: null },
    errors: { default: () => {} },
  },
  data() {
    return {
      loading: false,
      isFocused: false,
    };
  },
  computed: {
    ...mapGetters({
      accounts: "financials/accounts",
      AccountsTree: "financials/AccountsTree",
    }),
    isAr() {
      return this.getLocale() == "ar";
    },
    parentCode() {
      let code = 0,
        level = 0,
        account_id = this.parentAccountId;
      if (account_id) {
        const accounts = this.accounts;
        const tree = Object.entries(this.AccountsTree);
        while (account_id) {
          level++;
          code += accounts[account_id].code;
          account_id = tree.find((acc) => acc[1].includes(account_id));
          account_id = account_id ? parseInt(account_id[0]) : null;
        }
      }
      return code ? parseInt(code.toString().substr(0, level)) : null;
    },
    code: {
      set(val) {
        this.$emit("input", parseInt(val));
      },
      get() {
        return this.value;
      },
    },
  },
  created: async function () {
    await this.$store.dispatch("financials/fetchAccounts");
    this.loading = false;
  },
};
</script>