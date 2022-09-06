<template>
  <div class="bg-white p-3 p-md-5">
    <clip-loader class="my-4" v-if="loading" />

    <template v-else>
      <main>
        <!-- show (no X yet) message -->
        <p
          v-if="models && !obj_length(models) && (notYetAttr || attr || (link && attr))"
          class="font-lg text-secondary"
        >
          <span
            class="mr-1"
            v-text="`${ucFirst($t('noYet', { attr: notYetAttr || attr }))}.`"
          />
          <router-link
            v-if="notYetAttr && link && (!permission || can(`${permission}.store`))"
            class="text-primary"
            :to="link"
            v-text="$t('wantToAddX', { attr: attr || notYetAttr })"
          />
        </p>

        <!-- free slot -->
        <slot v-else />
      </main>
    </template>
  </div>
</template>

<script>
import Widget from "../ControlPanel/components/Widget.vue";
export default {
  name: "client-page",
  props: {
    value: { required: false },
    title: { default: null },
    link: { default: () => {} },
    models: { default: null },
    attr: { default: null },
    notYetAttr: { default: null },
    onCreatedActions: { default: () => [], type: Array },
    permission: { default: null },
    noMargin: { default: null },
  },
  computed: {
    loading: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  created: function () {
    this.loading = true;
    let length = this.onCreatedActions.length;
    if (length) {
      this.onCreatedActions.forEach(async (action, i) => {
        if (i < length - 1) await this.$store.dispatch(action);
        else
          await this.$store.dispatch(action).then(() => (this.loading = false));
      });
    } else this.loading = false;
  },
  components: { Widget },
};
</script>