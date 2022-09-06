<template>
  <widget :title="title" v-bind="$attrs" :on-created-actions="onCreatedActions" v-model="loading">
    <slot name="before-form" />

    <b-form :loading="loading || !created" :action="_action" @submit="onSubmit" @reset="onReset" :disabled="disabled">
      <slot />
    </b-form>

    <slot name="after-form" />

    <b-button class="close-btn bg-all-none border-0 position-absolute position-top-right m-4" @click="onReset">
      <b-i icon="times" />
    </b-button>
  </widget>
</template>

<script>
import Widget from "../../../masters/ControlPanel/components/Widget";
export default {
  name: "create-page",
  props: {
    value                 : { required: true },
    onCreatedActions      : { default: () => [], type: Array },
    module                : { default: null },
    modulePostfix         : { default: '' },
    errors                : { default: () => {} },
    title                 : { default: null },
    route                 : { default: null },
    permission            : { default: null },
    action                : { default: null },
    id                    : { default: null },
    disabled              : { default: false },
    mountedPreprocessing  : { type: Function, default: () => {} },
    mountedPostprocessing : { type: Function, default: () => {} },
    submitPreprocessing   : { type: Function, default: () => {} },
    submitPostprocessing  : { type: Function, default: () => {} },
  },
  data() {
    return {
      created: false,
      loading: true,
    };
  },
  computed: {
    form: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    err: {
      set(val) {
        this.$emit("errors", val);
      },
      get() {
        return this.errors;
      },
    },
    _action() {
      return this.action || this.$route.params.action;
    },
    _id() {
      return this.id || this.$route.params.id;
    },
  },
  mounted: async function () {
    this.loading = true;
    
    // if (this.permission && !this.can(`${this.permission}.${this.action == 'update' ? 'update' : 'store'}`))
      // this.$router.push({ name: `${this.route || this.module}.index` })
      // this.$router.push({ name: '403', params: { route: this.$route.name }});

    // else
    // {
      await this.mountedPreprocessing();
      if (this._action === "update" && this._id) {
        await this.$store
          .dispatch(`${this.module}/find${this.modulePostfix}`, this._id)
          .then((model) => this.form = { ...this.form, ...model })
          .catch(() => this.$router.push({ name: `${this.route || this.module}.index` }));
      }
      await this.mountedPostprocessing();
      this.created = true;
      this.loading = false;
    // }
  },
  methods: {
    onSubmit: async function () {
      this.loading = true;
      this.submitPreprocessing();
      await this.$store
        .dispatch(`${this.module}/${this._action}${this.modulePostfix}`, this.form)
        .then((model) => this.$emit("submit", model))
        .catch((errors) => (this.err = errors));
      this.submitPostprocessing();
      this.loading = false;
    },
    onReset: function () {
      this.$emit("reset");
    },
  },
  components: {
    Widget,
  },
};
</script>

<style lang="scss">
.widget.no-title > header {
  display: none !important;
}

.widget .close-btn {
  display: none;
}

.floating-form .widget {
  position: relative;

  .close-btn {
    display: block;
  }
}
</style>