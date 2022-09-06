<template>
  <div
    :style="select_input && !wXS
      ? `top: ${select_input.coords.top}px; left: ${select_input.coords.left}px; width: ${select_input.coords.width}px`
      : `top: 50%; left: 50%; transform: translate(-50%, -50%);`
    "
    @click="e => $emit('click', e)"
  >
    <b-input
      v-model="search"
      ref="searchInput"
      class="mb-3 shadow"
      :placeholder="select_input.optionsByValues[select] ? select_input.optionsByValues[select].label : '--'"
      :disabled="select_input.disabled"
      :input-class="{ 'custom-select' : select_input.hasArrows }"
    />
    
    <div
      v-show="select_input.focused && !select_input.disabled"
      :id="select_input.id"
      class="max-ht-350 wd-280 bg-white index-up rounded-bottom shadow font-md p-2"
      style="overflow-y: scroll;"
    >
      <template v-if="results.length">
        <div
          v-for="(result, i) in results"
          :key="i"
          class="px-2 py-1 c-ptr"
          :class="[{'text-secondary-6 bg-light c-not-allowed' : result.disabled}, hovered_at == i ? 'bg-info text-white' : 'bg-hover-light']"
          v-text="result.label"
          @click="result.disabled ? null : selectResult(result.value)"
          @mouseover="result.disabled ? null : hovered_at = i"
          @mouseleave="result.disabled ? null : hovered_at == i ? null : hovered_at"
        />
      </template>
      <div class="px-2 py-3 bg-light" v-else v-text="$t('noMatches', { attr: search })" />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "b-select-form",
  data() {
    return {
      search: null,
      hovered_at: null,
    };
  },
  computed: {
    ...mapGetters({
      select_inputs: 'select',
    }),
    select_input() {
      return Object.values(this.select_inputs).find(inp => inp.focused) || {};
    },
    select: {
      get() {
        return this.select_input.value;
      },
      set(value) {
        document.documentElement.removeEventListener('keyup', this.handleKeyboardEvents, false);
        this.$store.commit('SET_INPUT', { name: 'select', id: this.select_input.id, value: { ...this.select_input, value, focused: false }});
      },
    },
    results() {
      let query = this.search;
      if (!query) return this.select_input.options;
      query = query.toLowerCase();
      return this.select_input.options.filter(op => `${op.label}${op.plain}${op.value}`.toLowerCase().includes(query))
    },
  },
  mounted() {
    setTimeout(() => {
      if (this.$refs.searchInput && !this.wXS)
        this.$refs.searchInput.focus();
    }, 50);
  },
  methods: {
    selectResult: function (value) {
      this.select = value;
      this.focused = false;
      this.search = null;
      this.hovered_at = null;
    },
    handleKeyboardEvents: function (e) {
      switch (e.code)
      {
        case 'ArrowUp':
          this.hovered_at -= this.hovered_at > 0 ? 1 : 0;
          break;
        case 'ArrowDown':
          this.hovered_at += this.hovered_at < this.obj_length(this.select_input.options) - 1 ? 1 : 0;
          break;
        case 'Enter':
        case 'NumpadEnter':
          if (this.results && this.results[this.hovered_at])
            this.selectResult(this.results[this.hovered_at].value);
          break;
        case 'Escape':
          this.focused = false;
          this.hovered_at = null;
          break;
      }
    },
  },
  watch: {
    select_input: {
      handler: function (input) {
        if (input && input.focused)
          document.documentElement.addEventListener('keyup', this.handleKeyboardEvents, false);
      },
      immediate: true,
    },
    search: {
      handler: function (search) {
        let match = this.select_input.optionsByValues[search];
        if (match) this.selectResult(match.value);
      },
    },
  },
};
</script>