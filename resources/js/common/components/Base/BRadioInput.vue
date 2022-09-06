<template>
  <div class="b-radio-input">
    <div class="c-ptr" @click="input = val">
      <slot />
    </div>
    <input type="radio" v-model="input" :value="val" class="d-none" />
  </div>
</template>

<script>
export default {
  name: "b-radio-input",
  props: {
    value: { required: true },
    val: { required: true },
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
  },
};
</script>

<style>
</style>