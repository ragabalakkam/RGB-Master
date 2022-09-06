<template>
  <div class="multi-tab d-flex flex-column" :id="_id" @click="e => $emit('click', e)">
    <header class="d-flex" :class="`mb-${mb || padding} flex-gap-${padding}`">
      <b-button
        v-for="(tab, i) in tabs"
        :key="i"
        v-text="tab.name"
        class="text-capitalize bg-all-none border-top-0 border-left-0 border-right-0 border-1 px-1"
        :class="currentIndex === i ? 'border-info text-info text-focus-info' : 'text-secondary-8'"
        @click="currentIndex = i"
      />
    </header>
    <main class="flex-1 overflow-hidden">
      <div v-show="!loading">
        <div
          class="d-flex"
          :class="{ 'transition-0' : loading }"
          :style="`
            gap: ${gap}%;
            width: ${(tabs.length * 100) + (gap * (tabs.length - 1))}%;
            margin-${getLocale() == 'ar' ? 'right' : 'left'}: -${(currentIndex * 100) + (gap * (currentIndex))}%
          `"
        >
          <slot />
        </div>
      </div>
      <div v-show="loading">
        <clip-loader />
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: "multi-tab",
  props: {
    value:    { default: 0 },
    padding:  { default: 3 },
    gap:      { default: 0 },
    mb:       { default: 3 },
  },
  data() {
    return {
      tabs: [],
      loading: true,
    };
  },
  computed: {
    currentIndex: {
      set(value) {
        this.$emit("input", value);
      },
      get() {
        return this.value || 0;
      },
    },
    _id() {
      return `multi-tab-${Math.ceil(Math.random() * 1000000)}`;
    },
  },
  methods: {
    hideNonSelectedTabs: function (value = this.currentIndex) {
      document.querySelectorAll(`#${this._id} .tab:not(:nth-child(${value + 1})) > .tab-content`).forEach(el => el.classList.add('d-none'));
      document.querySelectorAll(`#${this._id} .tab:nth-child(${value + 1}) > .tab-content`).forEach(el => el.classList.remove('d-none'));
    },
  },
  watch: {
    tabs: {
      handler: function (newVal) {
        if (newVal) {
          setTimeout(() => {
            this.tabs = this.$children.filter(child => child.$options._componentTag === "tab");
            this.loading = false;
            this.hideNonSelectedTabs();
          }, 300);
        }
      },
      immediate: true,
    },
    currentIndex: {
      handler: 'hideNonSelectedTabs',
      immediate: true,
    },
  },
};
</script>