<template>
  <b-input
    type="tel"
    v-model="input"
    v-bind="$attrs"
    :input-dir="input ? '' : 'ltr'"
    :placeholder="`966 ${'x'.repeat(2)} ${'x'.repeat(7)}`"
    :label="label == 'phone' ? $t('phone') : label"
    :attr="attr == 'phone' ? $t('phone') : attr"
    :name="name"
    :errors="errors"
    :attrs="{value: errors[name] && errors[name][0] == 'digits' ? '12' : '966'}"
    @keydown="handlekeydown"
  />
</template>

<script>
export default {
  name: 'phone-input',
  props: {
    value : { default: '' },
    errors: { default: () => {}, type: Object },
    name: { default: 'phone' },
    label: { default: 'phone' },
    attr: { default: 'phone' },
  },
  computed: {
    input: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
  },
  data() {
    return {
      chars: "abcdefghijklmnopqrstuvwxyz-@#$%^&*():;/~'\"",
    };
  },
  methods: {
    handlekeydown: function (e) {
      if (this.chars.includes(e.key) && !(e.ctrlKey && ['a', 'c', 'v'].includes(e.key)))
        e.preventDefault();
    },
  },
}
</script>