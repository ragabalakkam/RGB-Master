<template>
  <b-form-group :required="is_required">
    <div
      class="d-flex flex-column-xs"
      :class="[`flex-gap-${gap ? gap : wXS || flexColumn ? showLabels ? 3 : 2 : 'bs'}`, { 'flex-column' : flexColumn}]"
      v-if="model"
    >
      <div class="flex-1" v-for="locale in locales" :key="locale.id">
        <b-textarea
          class="mb-0"
          :rows="rows"
          :name="name"
          :text-class="`${errors && errors.name && (!model[locale.label] || errors.name.includes(`unique.${locale.label}`)) ? 'is-invalid' : ''} ${noResize === '' ? 'no-resize' : ''}`"
          :show-label="showLabels"
          :label="$t('XInLocaleY', { x: name, y: $t(locale.label) })"
          :placeholder="showLabels ? '' : $t('XInLocaleY', { x: name, y: $t(locale.label) })"
          :required="is_required"
          v-model="model[locale.label]"
        />
      </div>
    </div>
    <b-error v-if="errors && errors.name" :field="errors.name" :attr="name" />
  </b-form-group>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "multi-lang-input",
  props: {
    value     : { required: true },
    errors    : { default: () => {} },
    showLabels: { default: true },
    gap       : { default: null },
    name      : { default: 'name' },
    rows      : { default: 1 },
    noResize  : { default: null },
    flexColumn: { default: false },
    required  : { default: false },
  },
  computed: {
    ...mapGetters({
      locales: "locales/locales",
    }),
    model: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    is_required() {
      return this.required || this.required === '';
    },
  },
  mounted: function () {
    let value = {};
    Object.values(this.locales).forEach(
      locale => value[locale.label] = this.model ? this.model[locale.label] : null
    );
    this.model = value;
  },
};
</script>

<style scoped>
textarea.no-resize {
  resize: none;
}
</style>