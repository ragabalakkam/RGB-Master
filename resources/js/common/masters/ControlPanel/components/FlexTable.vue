<template>
  <pagination :items-per-page="itemsPerPage" :class="`${paginationClass}`" ref="pagination">

    <!-- search -->
    <template v-if="hasSearch && obj_length(data)">
      <div class="d-flex align-items-center flex-gap-2">
        <div class="flex-1 position-relative">
          <b-input
            v-model="search"
            :placeholder="$t('searchByX', { x: (searchBy
              ? Object.values(searchBy).map(x => head[x]).join(' / ')
              : Object.entries(head).filter(x => x[0] != 'actions').map(x => x[1]).join(' / '))
            })"
          />
          <div class="position-absolute position-top-right ht-form-control d-flex-center flex-gap-2 px-3">
            <b-i icon="filter" :class="`text-secondary${search ? '' : '-3'}`" />
            <span v-if="false" class="text-secondary-6" v-text="`(${results.length}) ${$t('results')}`" />
          </div>
        </div>
      </div>

      <hr>
    </template>

    <template v-if="results && results.length">
      <!-- header -->
      <div class="d-flex pb-2 border-bottom text-capitalize bg-info-1 px-2" :class="`flex-gap-${gap} py-${py}`">
        <!-- <div class="wd-30"><b-checkbox v-model="select" /></div> -->
        <div v-if="Object.keys(head).includes('index') || Object.keys(head).includes('id')" :class="`id-field ${hidden('id')}`" v-text="'#'" />

        <div
          v-for="(element, i) in Object.entries(head).filter(c => c[0] != 'actions' && c[0] != 'id' && c[0] != 'index')"
          :key="i"
          :class="`${element[0] == 'image' ? 'img-field' : (classes ? (classes[element[0]] || 'flex-1') : 'flex-1')} ${hidden(element[0])}`"
          v-html="element[1]"
        />

        <div
          v-if="Object.keys(head).includes('actions') && (canUpdate || canDelete || _actions)"
          :class="`${_actions && classes ? (classes['actions'] || 'flex-1') : 'actions-field'} ${hidden('actions')}`" v-t="'actions'"
        />
      </div>

      <!-- results -->
      <pagination-item
        v-for="(element, i) in results"
        :key="element.id"
        class="border-bottom p-2 bg-white"
        :class="[`py-${py} ${paginationItemClass}`, { 'c-ptr text-hover-primary' : onItemClicked }]"
        @click="onItemClicked ? onItemClicked(element) : null"
      >
        <div :class="`d-flex flex-gap-${gap} align-items-center`">
          <!-- <div class="wd-30"><b-checkbox v-model="select[element.id]" /></div> -->
          <div v-if="Object.keys(head).includes('id')" :class="`id-field ${hidden('id')}`" v-text="i + 1" />
          <div v-else-if="Object.keys(head).includes('index')" :class="`id-field ${hidden('id')}`" v-text="element.id" />

          <div
            v-for="(col, i) in Object.keys(head).filter(c => c != 'actions' && c != 'id' && c != 'index')"
            :key="i"
            :class="`${col == 'image' ? 'img-field' : (classes ? (classes[col] || 'flex-1') : 'flex-1')} ${hidden(col)}`"
          >
            <div v-if="col != 'image'" :class="{ 'text-secondary-7' : !cast(col, element[col], element) }" v-html="cast(col, element[col], element) || $t('thereIsNoX', { x: '' })" />
            <b-img v-else-if="element[col]" class="rounded overflow-hidden" :src="`/storage/${element[col].src}`" size="40" v-open-in-viewer />
            <p class="text-secondary-5 ht-40 d-flex align-items-center" v-else v-text="$t('thereIsNoX', { x: '' })" />
          </div>
          
          <div
            v-if="Object.keys(head).includes('actions') && (canUpdate || canDelete || _actions)"
            :class="`${_actions && classes ? (classes['actions'] || 'flex-1') : 'actions-field'} d-flex flex-gap-2 ${hidden('actions')}`"
          >
            <!-- update -->
            <b-button
              v-if="canUpdate"
              variant="info"
              class="text-white sz-30"
              size="sm"
              :title="$t('edit')"
              @click.stop="editBtnClicked ? editBtnClicked(element) : $router.push({
                name: `${_route}.create`,
                params: { action: 'update', id: element.id, ...(additionalParams ? additionalParams(element) : {}) }
              })"
            >
              <b-i icon="pen-alt" class="mr-1" />
            </b-button>

            <!-- delete -->
            <b-button
              v-if="canDelete"
              variant="danger"
              size="sm"
              class="sz-30"
              :title="$t('delete')"
              @click.stop="confirmDelete(element, `${module}/delete${deletePostfix}`, element.name || element.id, additionalParams ? additionalParams(element) : {})"
            >
              <b-i icon="trash" class="mr-1" />
            </b-button>

            <!-- more actions -->
            <template v-if="actions">
              <b-button
                v-for="(action, i) in actions"
                :key="i"
                :variant="typeof action.variant == 'function' ? action.variant(element) : action.variant"
                :icon="action.icon"
                :class="action.class"
                class="ht-30 py-1"
                size="sm"
                @click.stop="action.func(element)"
              >
                <span v-if="action.text" v-html="typeof action.text == 'function' ? action.text(element) : action.text" />
              </b-button>
            </template>
          </div>
        </div>
      </pagination-item>
    </template>

    <p v-else-if="search" class="bg-light p-2" v-text="$t('noMatches', { attr: search })" />
    <p v-else class="py-2 text-secondary-7" v-text="$t('noYet', { attr: $t('results') })" />
  </pagination>
</template>

<script>
const Pagination = () => import("../../../vendor/Pagination/Pagination.vue");
const PaginationItem = () => import("../../../vendor/Pagination/PaginationItem.vue");
export default {
  name: "flex-table",
  props: {
    head                : { default: () => {} },
    data                : { default: () => {} },
    casts               : { default: () => {} },
    classes             : { default: () => {} },
    gap                 : { default: 3 },
    py                  : { default: 2 },
    paginationClass     : { default: '' },
    paginationItemClass : { default: '' },
    module              : { default: null },
    permission          : { default: null },
    route               : { default: null },
    deletePostfix       : { default: '' },
    additionalParams    : { default: () => {} },
    hiddenXs            : { default: () => {} },
    itemsPerPage        : { default: 7 },
    hasUpdateBtn        : { default: true },
    hasDeleteBtn        : { default: true },
    hasSearch           : { default: true },
    onItemClicked       : { default: null, type: Function },
    editBtnClicked      : { default: null },
    searchBy            : { default: null },
    actions             : { default: null },
  },
  data() {
    return {
      search: null,
      // select: {},
    };
  },
  computed: {
    filteredHead() {
      return Object.values(this.head).filter(c => c != this.$t('actions') && c != this.$t('id'));
    },
    _route() {
      return this.route || this.module;
    },
    results() {
      let data = this.withoutTrashed(this.data),
        search = this.search ? this.search.toLowerCase() : null;
      return search ? data.filter(d => JSON.stringify(d).toLowerCase().includes(search)) || [] : data;
    },
    canUpdate() {
      return this.hasUpdateBtn && (!this.permission || this.can(`${this.permission}.update`));
    },
    canDelete() {
      return this.hasDeleteBtn && (!this.permission || this.can(`${this.permission}.destroy`));
    },
    _actions() {
      return this.actions ? this.actions.filter(a => !a.hasOwnProperty('if') || a.if) : null;
    },
  },
  methods: {
    cast: function (key, value, element) {
      if(this.casts && Object.keys(this.casts).includes(key)) {
        try { return this.casts[key](value, element) } catch (e) {}
      }
      return value;
    },
    hidden: function (key) {
      return this.hiddenXs && this.hiddenXs.includes(key) ? 'hidden-xs' : '';
    },
  },
  watch: {
    search: function(val) {
      this.$emit('search', val);
    },
    results: {
      handler: function () {
        if (this.$refs.pagination) this.$refs.pagination.refresh();
      },
      deep: true,
    },
  },
  components: {
    PaginationItem,
    Pagination,
  },
};
</script>