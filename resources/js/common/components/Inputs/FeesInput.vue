<template>
  <b-form-group :label="$t('ال') + $t('fees')">
    <div class="bg-light rounded p-2 border">
      <!-- branch_fees -->
      <div
        class="d-flex flex-wrap flex-gap-2 pb-2 mb-2 border-bottom"
        v-for="(branch_fee, i) in withoutTrashed(branch_fees).sort((a, b) => a.order < b.order ? -1 : 1)"
        :key="i"
      >
        <!-- order -->
        <div class="text-center wd-40 d-inline-block d-flex flex-column border">
          <b-button size="sm" class="bg-white flex-1 p-0 d-flex-center" @click="changeOrder(i, -1)"><b-i icon="caret-up" /></b-button>
          <b-button size="sm" class="bg-white flex-1 p-0 d-flex-center" @click="changeOrder(i, 1)"><b-i icon="caret-down" /></b-button>
        </div>

        <!-- select fee -->
        <b-select class="flex-1 mb-0" v-model="branch_fee.fee_id" :null-option-attr="$t('fee')">
          <option
            v-for="fee in getIterableFees(branch_fee.fee_id)"
            :key="fee.id"
            :value="fee.id"
            v-text="`${fee.value}${fee.option == 'ratio' ? '%' : ' ' + parseName(currency.code)} - ${parseName(fee.name)}`"
          />
        </b-select>

        <!-- remove fee -->
        <b-button variant="outline-danger" @click="removeFee(i)">
          <b-i icon="trash-alt" />
        </b-button>
      </div>

      <!-- add a new fee -->
      <b-button variant="outline-primary" class="d-flex-center" @click="addFee" :disabled="disabled || !getIterableFees().length">
        <b-i icon="plus" class="mr-2" /> {{ $t('applyX', { x: $t('newX', { x: $t('fee') })}) }}
      </b-button>
    </div>
  </b-form-group>
</template>

<script>
import { mapGetters } from 'vuex';
const NameInput = () => import('./NameInput.vue');
export default {
  name: "branch_fees-input",
  props: {
    value: { type: Array, default: () => [] },
  },
  data() {
    return {
      errors: {},
    };
  },
  created: async function () {
    this.$store.dispatch('branches/fetchFees');
  },
  computed: {
    ...mapGetters({
      fees: 'branches/fees',
      currency: 'user/currency',
    }),
    branch_fees: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
    disabled() {
      return !!this.branch_fees.filter(f => !f.fee_id).length;
    },
  },
  methods: {
    getIterableFees: function (id = null) {
      let used_fees = [];
      this.branch_fees.filter(f => f.fee_id != id).forEach(f => used_fees.push(f.fee_id));
      return this.withoutTrashed(this.fees).filter(f => !used_fees.includes(f.id));
    },
    changeOrder: function (index, change) {
      let newVal = parseInt(this.branch_fees[index].order) + change;
      if (newVal > 0 && newVal < this.branch_fees.length + 1) {
        this.branch_fees[newVal - 1].order = this.branch_fees[index].order;
        this.branch_fees[index].order = newVal;
      }
    },
    addFee: function () {
      if (!this.disabled)
        this.branch_fees.push({ fee_id: null, order: this.branch_fees.length + 1});
    },
    removeFee: function (index) {
      this.branch_fees.splice(index, 1);
    },
  },
  components: {
    NameInput,
  },
};
</script>