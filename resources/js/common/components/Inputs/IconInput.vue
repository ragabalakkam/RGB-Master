<template>
  <div class="position-relative">
    <b-input input-class="pr-5" type="text" v-bind="$attrs" v-model="input" />
    <div class="position-absolute position-bottom-right" :class="overlayClass">
      <a
        v-if="showLink"
        class="text-primary fs-3"
        :href="`https://fontawesome.com/icons?q=${input || ''}`"
        target="_blank"
      >fontawesome.com</a>

      <div class="form-control p-0 d-flex-center wd-35 border-0 bg-none ml-auto">
        <b-i :icon="input" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'icon-input',
  props: {
    value         : { required: true },
    showLink      : { default: true },
    overlayClass  : { default: '' },
  },
  computed: {
    input: {
      set(val) {
        this.$emit('input', val);
      },
      get() {
        return this.value;
      },
    },
  },
}
</script>