<template>
  <b-form-group
    class="d-flex form-control p-0 ht-form-control"
    :class="{ 'border-primary': isFocused }"
    @click="e => $emit('click', e)"
  >
    <label
      v-if="label"
      class="px-2 d-flex-center h-100 rounded-left c-ptr mb-0 fs-3"
      :class="[`bg-${variant}`, labelClass]"
      v-html="label"
      :for="id"
    />

    <b-input
      :id="_id"
      class="flex-1 h-100 bg-none"
      :input-class="`border-0 h-100 ${inputClass}`"
      v-bind="$attrs"
      v-model="model"
      :show-label="false"
      @blur="isFocused = false"
      @focus="e => { $emit('focus', e); isFocused = true; }"
    />
  </b-form-group>
</template>

<script>
export default {
  name: "b-labeled-input",
  props: {
    value     : { required: true },
    label     : { default: null },
    id        : { default: null },
    variant   : { default: 'light' },
    labelClass: { default: '' },
    inputClass: { default: '' },
  },
  data() {
    return {
      isFocused: false,
    };
  },
  computed: {
    model: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    _id() {
      return this.id || `input-${this.name}-${Math.ceil(Math.random() * 1000)}`;
    },
  },
};
</script>

<style></style>