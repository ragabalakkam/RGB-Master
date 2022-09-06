<template>
  <div :id="id" class="carousel overflow-hidden d-flex flex-column flex-gap-bs" :style="cssVars">
    <div v-if="loading" class="h-100 d-flex-center">
      <clip-loader />
    </div>

    <template v-else>
      <div class="flex-1">
        <div class="carousel-content d-flex-center flex-1">
          <slot />
        </div>
      </div>

      <div class="d-flex-center flex-gap-2">
        <div
          v-for="p in items.length"
          :key="p" @click="moveToPage(p)"
          class="ht-10 rounded-edges c-ptr"
          :class="`bg-${current_page == p ? 'secondary-4 wd-40' : 'light bg-hover-secondary-4 wd-10 border'}`"
        />
      </div>      
    </template>
  </div>
</template>

<script>
export default {
  name: "carousel",
  props: {
    value: { default: 1 },
    speed: { default: 1 },
    duration: { default: 1.5 },
  },
  data() {
    return {
      items: [],
      loading: false,
      id: `carousel-${Math.ceil(Math.random() * 1000)}`,
      timeout: null,
    };
  },
  computed: {
    cssVars() {
      const count = this.items.length;
      const gap = 2;
      const cur = this.current_page;
      return {
        '--carousel-gap': `${gap}%`,
        '--carousel-speed': `margin ${this.speed}s ease-in-out`,
        '--carousel-width' : `${(count * 100) + ((count - 1) * gap)}%`,
        '--carousel-margin' : `${((cur - 1) * gap) + ((cur - 1)* 100)}%`,
      };
    },
    current_page: {
      set(val) {
        this.$emit('input', val);
      },
      get() {
        return this.value;
      },
    },
  },
  mounted: async function () {
    this.refresh();
  },
  methods: {
    moveToPage: function (pageNom) {
      this.current_page = pageNom;
    },
    moveToNextPage: function () {
      const cur = this.current_page;
      this.moveToPage(cur >= this.items.length ? 1 : cur + 1);
    },
    refresh: async function () {
      this.loading = true;
      this.items = Object.values(document.querySelectorAll(`#${this.id} .carousel-content .custom-carousel-item`));
      this.moveToPage(this.current_page || 1);
      this.loading = false;
    },
  },
  watch: {
    current_page: {
      handler: function () {
        if (this.timeout) clearTimeout(this.timeout);
        this.timeout = setTimeout(this.moveToNextPage, this.speed * this.duration * 1000);
      },
      immediate: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.carousel-content {
  transition: var(--carousel-speed);
  gap: var(--carousel-gap);
  width: var(--carousel-width);
}

html[dir=ltr] .carousel-content {
  margin-right: var(--carousel-margin);  
}

html[dir=rtl] .carousel-content {
  margin-left: var(--carousel-margin);
}
</style>