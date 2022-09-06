<template>
  <div
    v-bind="$attrs"
    class="d-flex"
    :class="[{ 'h-100 flex-gap-bs' : isMobile }, { 'flex-column' : wXS }]"
    :style="`height: ${settings[`${_device}_has_grid`] ? 'auto' : wXS ? '175vh' : '87.5vh'}; gap: ${settings.desktop_grid_gap};`"
  >
    <div class="flex-1 d-flex flex-column">
      <div class="row mb-3">
        <div
          v-for="i in Object.values(settings.desktop_headers || {}).filter(val => val).length - 2" :key="i"
          :class="`col-${isMobile ? 6 : Math.ceil(12 / settings.desktop_fields_in_row)} mb-3`"
        >
          <div class="rounded border bg-dark-3 p-1 mb-1 wd-90" />
          <div class="rounded border bg-dark-3 p-3" />
        </div>
        <div class="col mb-3">
          <div class="rounded border bg-dark-3 p-1 mb-1 wd-90" />
          <div class="rounded border bg-dark-3 p-3" />
        </div>
      </div>
      <div class="flex-1 bg-dark-3 rounded border" />
    </div>

    <div
      v-if="settings[`${_device}_has_grid`]"
      :style="cssVars"
      class="d-flex h-100 text-white position-relative"
      :class="{ 'flex-1' : isMobile }"
    >
      <!-- COL 1 | select main category -->
      <div v-if="mainCategoriesCols" :class="`flex-${mainCategoriesCols} grid-container overflow-scroll`">
        <div class="grid" :style="getCssVars(mainCategoriesCols)">
          <!-- cashier button -->
          <div class="btn btn-success button d-flex-center">
            <b-i icon="cash-register" size="2x" />
          </div>
          <!-- main categories -->
          <div
            class="btn button main-category"
            v-for="i in mainCategoriesCols * rows - 1"
            :key="`empty-main-category-${i}`"
          />
        </div>
      </div>

      <!-- COL 2 | loop on selected sub-categories -->
      <div v-if="subCategoriesCols" :class="`flex-${subCategoriesCols} grid-container overflow-scroll position-relative`">
        <div class="grid" :style="getCssVars(subCategoriesCols)">
          <!-- sub categories -->
          <div
            class="btn button sub-category"        
            v-for="i in subCategoriesCols * rows"
            :key="`empty-sub-category-${i}`"
          />
        </div>
      </div>

      <!-- COL 3 | loop on selected products -->
      <div :class="`flex-${cols - mainCategoriesCols - subCategoriesCols} grid-container overflow-scroll position-relative`">
        <div class="grid" :style="getCssVars(cols - mainCategoriesCols - subCategoriesCols)">
          <!-- products -->
          <div
            class="btn button product"
            v-for="i in remaining_rows"
            :key="`empty-product-${i}`"
          />
        </div>
      </div>

      <!-- when disabled -->
      <div class="position-absolute position-top-left h-100 w-100 d-flex-center bg-white-3" v-if="disabled" />
    </div>
  </div>
</template>

<script>
export default {
  name: "products-grid",
  props: {
    settings  : { default: () => {} },
    device    : { default: null },
    disabled  : { default: false },
  },
  computed: {
    _device() {
      return this.device || (this.wXS ? 'mobile' : this.wMD ? 'tablet' : 'desktop');
    },
    isDesktop() {
      return this._device == 'desktop';
    },
    isTablet() {
      return this._device == 'tablet';
    },
    isMobile() {
      return this._device == 'mobile';
    },

    cols() {
      return this.settings[`${this._device}_cols`] || 7;
    },
    rows() {
      return this.settings[`${this._device}_rows`] || 7;
    },
    gap() {
      return '0.5rem';
    },
    mainCategoriesCols() {
      return this.settings[`${this._device}_main_categories_cols`] || 1;
    },
    subCategoriesCols() {
      return this.settings[`${this._device}_sub_categories_cols`] || 1;
    },
    mainCategoriesBg() {
      return this.settings.main_category_bg || "var(--bs-info)";
    },
    mainCategoriesFg() {
      return this.settings.mainCategoriesFg || "var(--bs-white)";
    },
    selectedMainCategoryBg() {
      return this.settings.selected_main_category_bg || "var(--bs-danger)";
    },
    selectedMainCategoryFg() {
      return this.settings.selected_main_category_bg || "var(--bs-white)";
    },
    subCategoriesBg() {
      return this.settings.sub_category_bg || "var(--bs-primary)";
    },
    subCategoriesFg() {
      return this.settings.sub_category_fg || "var(--bs-white)";
    },
    selectedSubCategoryBg() {
      return this.settings.selected_sub_category_bg || "var(--bs-secondary)";
    },
    selectedSubCategoryFg() {
      return this.settings.selected_sub_category_fg || "var(--bs-white)";
    },
    productsBg() {
      return this.settings.product_bg || "var(--bs-info)";
    },
    productsOverlayBg() {
      return this.settings.product_overlay_bg || "var(--bs-black)";
    },
    productsOverlayFg() {
      return this.settings.product_overlay_fg || "var(--bs-white)";
    },
    overlayTransparency() {
      return this.settings.overlay_transparency || 0.5;
    },

    cssVars() {
      return {
        'width'                       : this.wXS ? 'unset' : this.settings.desktop_grid_width,

        '--rows'                      : this.rows,
        '--gap'                       : this.gap,

        '--main-category-bg'          : this.mainCategoriesBg,
        '--main-category-fg'          : this.mainCategoriesFg,
        '--selected-main-category-bg' : this.selectedMainCategoryBg,
        '--selected-main-category-fg' : this.selectedMainCategoryFg,

        '--sub-category-bg'           : this.subCategoriesBg,
        '--sub-category-fg'           : this.subCategoriesFg,
        '--selected-sub-category-bg'  : this.selectedSubCategoryBg,
        '--selected-sub-category-fg'  : this.selectedSubCategoryFg,

        '--product-bg'                : this.productsBg,
        '--product-overlay-bg'        : `rgba(${this.hexToRgb(this.productsOverlayBg).join(', ')}, ${this.overlayTransparency})`,
        '--product-overlay-fg'        : this.productsOverlayFg,
      };
    },
    remaining_rows() {
      let cols = this.cols - this.mainCategoriesCols - this.subCategoriesCols,
        products_count = 0,
        max = this.rows * cols;
      while (max < products_count) { max+= cols }
      return max - products_count;
    },
  },
  methods: {
    getCssVars: function (cols) {
      const rows = this.rows;
      return {
        "--grid-cols" : `repeat(${cols}, 1fr)`,
        "--grid-rows" : `repeat(${rows}, 1fr)`,
        "--height"    : `${(rows * this.toPX(this.buttonHeight)) + (rows * this.toPX(this.gap))}px`,
      };
    },
    toPX: function (str) {
      if (str) {
        if(str.includes('rem'))
          return parseFloat(str.replace('rem', '')) * 16;
  
        else if (str.includes('px'))
          return parseFloat(str.replace('px', ''));
      }
      return str;
    },
    hexToRgb: function (hex) {
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
      ] : null;
    },
  },
};
</script>

<style lang="scss" scoped>
.fas,
.far,
.fal,
.fab {
  font-weight: 300;
}

.bg-black {
  background-color: var(--bs-black);
}

.c-auto:not(.disabled) {
  cursor: auto;
}

.grid {
  display: grid;
  height: 100%;
  grid-template-columns: var(--grid-cols);
  grid-template-rows: var(--grid-rows);
}

.pc-view .grid-container,
.tablet-view .grid-container {
  height: calc(100vh - 3.5rem - 3.25rem - 2.5rem);
}

.pc-view .grid-container .button,
.tablet-view .grid-container .button {
  height: calc((100vh - 3.5rem - 3.25rem - 2.5rem - ((var(--rows) - 1) * var(--gap))) / var(--rows));
}

.phone-view .grid-container {
  height: calc(100vh - 2rem - 2.5rem);
}

.phone-view .grid-container .button {
  height: calc((100vh - 2rem - 2.5rem - ((var(--rows) - 1) * var(--gap))) / var(--rows));
}

/* gap */

.d-flex {
  gap: var(--gap);
}

.grid {
  grid-gap: var(--gap);
}

/* */

.button {
  color: inherit;
  position: relative;
  padding: 0;
  border: none;
}

/* bg colors */

.main-category:not(.selected) {
  background-color: var(--main-category-bg);
  color: var(--main-category-fg);
}

.main-category.selected {
  background-color: var(--selected-main-category-bg);
  color: var(--selected-main-category-fg);
}

.sub-category:not(.selected) {
  background-color: var(--sub-category-bg);
  color: var(--sub-category-fg);
}

.sub-category.selected {
  background-color: var(--selected-sub-category-bg);
  color: var(--selected-sub-category-fg);
}

.product {
  background-color: var(--product-bg);

  .product-overlay {
    color: var(--product-overlay-fg);
    background-color: var(--product-overlay-bg);
  }
}
</style>