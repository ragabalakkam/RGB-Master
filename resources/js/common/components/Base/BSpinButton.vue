<template>
  <div class="b-spin-btn d-flex rounded-lg border" :style="cssVars">
    <b-button :variant="variant" :class="btnClass" :size="size" @click="input < max ? input += 1 : null" v-if="input < max">
      <b-i icon="plus" class="fs-2 font-900" />
    </b-button>

    <number-input-with-keybad
      v-show="showInput"
      type="number"
      class="flex-1 font-xl h-100"
      input-class="p-0 no-spinners rounded-0 border-0 text-center h-100"
      :min="min"
      :max="max"
      :step="step"
      :show-icon="false"
      v-model="input"
    />

    <b-button :variant="variant" :class="btnClass" :size="size" @click="input > min ? input -= 1 : null">
      <b-i icon="minus" class="fs-2 font-900" />
    </b-button>
  </div>
</template>

<script>
import NumberInputWithKeybad from '../NumberInputWithKeybad.vue';
export default {
  components: { NumberInputWithKeybad },
  name: "b-spin-button",
  props: {
    value     : { required: true },
    variant   : { default: "info" },
    min       : { default: 0 },
    max       : { default: 10000 },
    step      : { default: 1 },
    size      : { default: "md" },
    showInput : { default: true },
    width     : { default: '4.375rem' },
    height    : { default: '2.5rem' },
  },
  data() {
    return {
      isFocused: false,
    };
  },
  computed: {
    input: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value;
      },
    },
    btnClass() {
      return `wd-${this.wXS ? 15 : 20} p-0 rounded-0 no-shadow d-flex-center`;
    },
    cssVars() {
      return {
        '--spin-wd' : this.width,
        '--spin-ht' : this.height,
      };
    },
  },
  watch: {
    input: function () {
      this.$emit('change');
    },
  },
};
</script>

<style lang="scss" scoped>
.b-spin-btn {
  width: var(--spin-wd);
  height: var(--spin-ht);
}
</style>