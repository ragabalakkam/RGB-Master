<template>
  <b-form-group :label="$t('theAccount')">
    <div class="d-flex flex-gap-2 flex-column-xs" v-if="!loading">
      <b-select
        v-for="(level, i) in levels"
        :key="level"
        class="flex-1 mb-0"
        v-model="codes[i]"
        :errors="codes[i] ? {} : errors"
        name="account_id"
        :attr="$t('account')"
        :null-option-attr="$t('account')"
        :show-label="false"
        :show-error="false"
        :null-option-disabled="false"
      >
        <option
          v-for="account in accounts[i].filter(acc => acc.account_id == (i > 0 ? codes[i - 1] : null))"
          :key="account.id"
          :value="account.id"
          v-text="parseName(account.name)"
        />
      </b-select>
    </div>

    <b-error v-if="errors" :field="errors.account_id" attr="account" />
  </b-form-group>
</template>

<script>
import { mapGetters } from "vuex";
import BError from '../Base/BError.vue';
export default {
  components: { BError },
  name: "account-input",
  props: {
    value: { required: true },
    errors: { default: () => {} },
  },
  data() {
    return {
      loading: false,
      codes: [],
    };
  },
  computed: {
    ...mapGetters({
      all_accounts: "financials/accounts",
      AccountsTree: "financials/AccountsTree",
    }),
    accounts() {
      const all_accounts = this.all_accounts;
      let accounts = {};
      this.withoutTrashed(all_accounts).forEach((account) => {
        let level = 0,
          acc = account;
        while (acc.account_id) {
          level++;
          acc = all_accounts[acc.account_id];
        }
        if (!accounts[level]) accounts[level] = [];
        accounts[level].push(account);
      });
      return accounts;
    },
    parentCode() {
      let code = "",
        account_id = this.parentAccountId;
      if (account_id) {
        const accounts = this.accounts;
        const tree = Object.entries(this.AccountsTree);
        while (account_id) {
          code = `${accounts[account_id].code}${code}`;
          account_id = tree.find((acc) => acc[1].includes(account_id));
          account_id = account_id ? parseInt(account_id[0]) : null;
        }
      }
      return code;
    },
    levels() {
      const x = Object.keys(this.accounts);
      return parseInt(x[x.length - 1]) + 1;
    },
    account_id: {
      set(val) {
        this.$emit("input", parseInt(val));
      },
      get() {
        return this.value;
      },
    },
  },
  created: async function () {
    this.loading = true;
    await this.$store.dispatch("financials/fetchAccounts");
    for (var i = 0; i < this.levels; i++) {
      Vue.set(this.codes, i, null);
    }
    this.loading = false;
    if (this.account_id) {
      const accounts = this.all_accounts;
      let acc_id = this.account_id,
        parents = [];
      while (acc_id) {
        if (acc_id) parents.unshift(acc_id);
        acc_id = accounts[acc_id].account_id;
      }
      parents.forEach((parent_id, i) => Vue.set(this.codes, i, parent_id));
    }
    this.loading = false;
  },
  watch: {
    codes: {
      handler: function (codes) {
        const results = codes.filter((code) => code);
        this.account_id = results[results.length - 1];
      },
      deep: true,
    },
  },
};
</script>

<style>
</style>