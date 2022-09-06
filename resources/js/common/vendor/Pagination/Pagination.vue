<template>
  <div :id="id" class="b-pagination">
    <div v-if="loading" class="h-100 d-flex-center">
      <clip-loader />
    </div>

    <slot v-else />

    <!-- footer -->
    <div class="d-flex flex-gap-2 mt-4 position-relative">
      <!-- change per_page -->
      <div v-if="!wXS" class="flex-1 d-flex align-items-center">
        <p class="font-md text-secondary-7 mr-2" v-text="$t('perX', { x: $t('page') })" />
        <b-select
          class="ht-25"
          input-class="ht-25 py-0 bg-light"
          :data="[5,7,10,20,50,100].map(function (id) { return { id, name: id }; })"
          v-model="per_page"
        />
      </div>

      <!-- pagination numbers -->
      <div v-if="pagesCount > 1" class="flex-4 d-flex-center flex-gap-2">
        <!-- first page -->
        <b-button
          v-if="buttons[0] !== 1"
          variant="light"
          class="ht-30 py-0 px-2 rounded-edges"
          @click="moveToPage(1)"
        >
          <b-i icon="angle-double-left" />
        </b-button>

        <!-- prev button -->
        <b-button
          variant="light"
          class="p-0 sz-30 d-flex-center rounded-circle"
          @click="moveToPage(current_page - 1)"
          :disabled="current_page == 1"
        >
          <b-i icon="angle-left" />
        </b-button>

        <!-- pages -->
        <b-button
          :variant="i == current_page ? 'primary' : 'light'"
          class="p-0 sz-30 d-flex-center rounded-circle"
          v-for="i in buttons"
          :key="i"
          v-text="i"
          @click="moveToPage(i)"
        ></b-button>

        <!-- next button -->
        <b-button
          variant="light"
          class="p-0 sz-30 d-flex-center rounded-circle"
          @click="moveToPage(current_page + 1)"
          :disabled="current_page == pagesCount"
        >
          <b-i icon="angle-right" />
        </b-button>

        <!-- last page -->
        <b-button
          v-if="buttons[buttons.length - 1] != pagesCount"
          variant="light"
          class="ht-30 py-0 px-2 rounded-edges"
          @click="moveToPage(pagesCount)"
        >
          <b-i icon="angle-double-right" />
        </b-button>
      </div>

      <!-- move to page -->
      <form v-if="!wXS" class="flex-1 d-flex align-items-center justify-content-end" @submit.prevent="moveToPage(move_to_page)">
        <b-form-input
          type="number"
          v-model="move_to_page"
          class="d-inline-block wd-50 ml-2 ht-25 fs-4 no-spinners px-2 text-center bg-light"
          input-class="form-control-sm"
        />

        <b-button
          variant="light"
          class="bg-all-none border-0 no-shadow font-md fs-3"
          type="submit"
          :disabled="move_to_page < 1 || move_to_page > pagesCount"
        >
          <span v-text="ucFirst($t('go'))" class="mr-1" />
          <b-i icon="angle-right" />
        </b-button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "b-pagination",
  props: {
    itemsPerPage  : { default: 10 },
    currentPage   : { default: 1 },
  },
  data() {
    return {
      items: [],
      loading: false,
      per_page: null,
      move_to_page: 1,
      current_page: 1,
      buttons_count: 5,
      id: `pagination-${Math.ceil(Math.random() * 1000)}`,
    };
  },
  computed: {
    pagesCount() {
      return Math.ceil(this.items.length / this.per_page);
    },
    buttons() {
      const current = this.current_page;
      const pages_count = this.pagesCount;
      const count = this.buttons_count - 2;

      let buttons = [current],
          half_count = Math.ceil((count - 1) / 2),
          left_limit = half_count <= current - 1 ? half_count : current - 1,
          right_limit = current + count - left_limit <= pages_count ?  count - left_limit - 1 : pages_count - current;
      
      if(left_limit + right_limit + 1 < count && count <= pages_count) left_limit += count - left_limit - right_limit - 1;

      for (var index = current - 1; left_limit ; index--, left_limit-- ) { buttons.unshift(index) }
      for (var index = current + 1; right_limit; index++, right_limit--) { buttons.push(index) }
      
      if(buttons[0] != 1) buttons.shift();
      if(buttons[buttons.length - 1] != pages_count) buttons.pop();

      return buttons;
    },
  },
  mounted: async function () {
    this.refresh();
    this.per_page = this.itemsPerPage;
  },
  methods: {
    moveToPage: function (pageNom) {
      if (pageNom < 1) pageNom = 1;
      else if (pageNom > this.pagesCount) pageNom = this.pagesCount;

      const itemsLength = this.items.length;
      const per_page = parseInt(this.per_page);

      const loops_to = pageNom * per_page;
      const loops_from = loops_to - per_page;

      for (var i = 0; i < itemsLength; i++) {
        if (i < loops_from || i >= loops_to) $(this.items[i]).hide();
        else $(this.items[i]).show();
      }

      this.current_page = pageNom;
    },
    refresh: function () {
      this.loading = true;
      setTimeout(() => {
        let items = Object.values($(`#${this.id} .pagination-item`))
        items.splice(0. -2);
        this.items = items;
        Object.values(this.items).forEach((item) => $(item).hide());
        this.moveToPage(this.current_page || 1);
      }, 50);
      this.loading = false;
    },
  },
  watch: {
    currentPage: {
      handler: function (val) { this.current_page = val },
      immediate: true,
    },
    current_page: {
      handler: function (val) { this.move_to_page = val },
      immediate: true,
    },
    wXS: {
      handler: function (val) { this.buttons_count = val ? 7 : 13; },
      immediate: true,
    },
    per_page: 'refresh',
  },
};
</script>