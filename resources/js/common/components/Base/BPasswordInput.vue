<template>
  <b-form-group :class="{ 'border-primary': focus }" :label="label" :required="is_required">
    <div class="d-flex overflow-hidden form-control p-0" :class="[{ 'border-danger' : errors && errors[name] }, inputClass]">
      <input
        class="flex-1 form-control rounded-0 border-0 pr-0"
        v-bind="$attrs"
        :type="type"
        v-model="model"
        :id="_id"
        autocomplete="false"
        spellcheck="false"
        @keydown.enter="$emit('enter')"
        @focus="focus = true"
        @blur="focus = false"
        :required="is_required"
      />
      <b-button
        variant="light"
        squared
        tabindex="-1"
        @click="type = type === 'password' ? 'text' : 'password'"
        class="bg-none border-0 text-dark-2 text-hover-secondary rounded-left-0 wd-50"
      >
        <b-i :icon="`eye${type === 'password' ? '' : '-slash'}`" />
      </b-button>
    </div>
    <b-error v-if="errors && showError && name" :field="errors[name]" :attr="attr || name" />
  </b-form-group>
</template>

<script>
export default {
  name: "b-password-input",
  props: {
    value     : { required: true },
    label     : { default: null },
    errors    : { default: null },
    name      : { default: 'password' },
    attr      : { default: 'password' },
    showError : { default: true },
    inputClass: { default: null },
    id        : { default: null },
    required  : { default: false },
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
      return this.id || 'password-' + Math.ceil(Math.random() * 10000);
    },
    is_required() {
      return this.required || this.required !== false;
    },
  },
  data() {
    return {
      type: "password",
      focus: false,
    };
  },
};
</script>