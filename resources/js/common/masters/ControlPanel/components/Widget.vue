<template>
  <div class="widget bg-white border rounded-lg shadow p-4" :class="[{'d-flex-center' : loading}, { 'my-3 my-xl-4' : noMargin !== '' }]">
    <clip-loader class="my-4" v-if="loading" />
    
    <template v-else>
      <header v-if="title">
        <div class="d-flex align-items-center">
          <!-- title -->
          <p class="font-xl flex-1" v-text="ucFirst(title)" />

          <!-- header span -->
          <slot name="header-span" />

          <!-- router link to create page (if exists) -->
          <router-link v-if="link && models && obj_length(models) && link" :to="link" class="btn btn-primary btn-sm ml-4">
            <b-i icon="plus" class="mr-1" />
            <span v-text="$t('createX', { attr })" />
          </router-link>
        </div>

        <hr class="border-x" />
      </header>

      <main class="h-100">
        <!-- show (no X yet) message -->
        <p class="font-lg text-secondary" v-if="models && !obj_length(models) && (notYetAttr || attr || (link && attr))">
          <span class="mr-1" v-text="`${ucFirst($t('noYet', { attr: notYetAttr || attr }))}.`" />
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
export default {
  name: "widget",
  props: {
    value             : { required: false },
    title             : { default: null },
    link              : { default: () => {} },
    models            : { default: null },
    attr              : { default: null },
    notYetAttr        : { default: null },
    onCreatedActions  : { default: () => [], type: Array },
    permission        : { default: null },
    noMargin          : { default: null },
  },
  computed: {
    loading: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
  },
  created: function () {
    this.loading = true;
    let length = this.onCreatedActions.length;
    if (length) {
      this.onCreatedActions.forEach(async (action, i) => {
        if (i < length - 1) await this.$store.dispatch(action)
        else await this.$store.dispatch(action).then(() => this.loading = false);
      });
    }
    else this.loading = false
    
  },
};
</script>

<style lang="scss" scoped>
  .widget.no-title > header {
    display: none;
  }
</style>