<template>
  <div class="position-relative h-100">
    <div class="position-relative h-100" @click="wXS || wSM || wMD ? (showKeypad = !showKeypad) : null">
      <b-input
        :id="id"
        type="number"
        form-class="h-100"
        v-bind="$attrs"
        v-model="input"
        :disabled="wXS"
        :input-class="`${inputClass} no-spinners ${showIcon ? 'pr-4' : ''}`"
        @blur="e => this.$emit('blur', e)"
        @change="e => this.$emit('change', e)"
      />
      <b-button
        v-if="showIcon"
        class="position-absolute position-top-right bg-white border-0"
        style="margin: 1px"
        @click.stop.prevent="showKeypad = !showKeypad"
      >
        <b-i icon="keyboard" />
      </b-button>
    </div>

    <!-- keypad -->
    <div
      v-if="showKeypad"
      id="keypad"
      class="position-absolute position-top d-flex flex-column ht-200 wd-160 index-up shadow"
      style="top: 2.5rem"
      dir="ltr"
      @blur="showKeypad = false"
    >
      <div class="flex-1 d-flex" v-for="(row, i) in buttons" :key="`row-${i}`">
        <b-button
          class="flex-1 d-flex-center border rounded-0"
          v-for="(btn, j) in row"
          :key="`btn-${j}`"
          v-text="btn"
          @click.stop="clicked(btn)"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "number-input-with-keybad",
  props: {
    value: { required: true },
    showIcon: { default: true },
    inputClass: { default: '' },
  },
  data() {
    return {
      buttons: [
        ["7", "8", "9"],
        ["4", "5", "6"],
        ["1", "2", "3"],
        [".", "0", "<"],
        ["SEL", "OK", "CR"],
      ],
      real_value: "",
      showKeypad: false,
    };
  },
  computed: {
    input: {
      set(val) {
        this.$emit("input", parseFloat(val));
      },
      get() {
        return this.value;
      },
    },
    id() {
      return `num-keypad-${Math.ceil(Math.random() * 100000)}`;
    },
  },
  methods: {
    clicked: function (val) {
      let realVal = this.real_value.toString();

      switch (val) {
        case "<":
          realVal = realVal.substr(0, realVal.length - 1);
          break;
        case ".":
          realVal = `${realVal}${realVal.includes(".") ? "" : "."}`;
          break;
        case "SEL":
          realVal = null;
          this.real_value = '0';
          document.getElementById(this.id).select();
          break;
        case "OK":
          this.showKeypad = false;
          break;
        case "CR":
          realVal = null;
          this.real_value = '0';
          this.input = '0';
          break;
        default:
          realVal = `${realVal || ""}${val}`;
      }

      if (realVal) {
        this.real_value = realVal;
        this.input = realVal.endsWith(".") ? `${realVal}${"0"}` : realVal || "0";
      }
    },
    eventListener: function (e) {
      this.showKeypad = false;
      this.$emit('blur', e);
    },
  },
  watch: {
    input: {
      handler: function (val) {
        this.real_value = val;
      },
      immediate: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.form-control:disabled,
.form-control[readonly] {
  background-color: var(--bs-white);
}
</style>