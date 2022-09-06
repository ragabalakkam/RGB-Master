<template>
  <div class="position-relative">
    <!-- input -->
    <b-input
      v-bind="$attrs"
      :input-class="search ? 'text-uppercase' : ''"
      list="select-product-by-name"
      @keydown.enter.prevent
      v-model="search"
    />

    <!-- datalist -->
    <datalist id="select-product-by-name">
      <option v-for="(option, i) in options" :key="i" :value="option.barcode" />
    </datalist>

    <!-- loading -->
    <clip-loader v-show="loading" size="14px" class="text-light position-absolute position-bottom-right mb-1 mr-2" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'select-item-by-barcode',
  props: {
    value           : { default: () => {}, type: Object },
    clearAfterSelect: { default: false },
  },
  data() {
    return {
      search: null,
      loading: false,
      timeout: null,
      options: [],
    };
  },
  computed: {
    ...mapGetters({
      products: 'products/products',
      variations: "products/variations",
      ingredients: 'ingredients/ingredients',
    }),
    input: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
  },
  watch: {
    search: async function (value)
    {
      value = value ? value.trim().toUpperCase() : value;
      
      let match = this.options.find(op => op.barcode == value)
        || this.withoutTrashed(this.variations).find(v => v.barcode == value)
        || this.withoutTrashed(this.ingredients).find(i => i.barcode == value);

      if (match)
      {
        this.input = { id: match.id, type: match.product_id ? 'product' : 'ingredient' };
        if (this.clearAfterSelect) setTimeout(() => this.search = null, 50);
      }
      
      else
      {
        this.input = { id: null, type: null };
        this.loading = true;
        if (this.timeout) clearTimeout(this.timeout);
        this.timeout = setTimeout(async () => {
          await axios.get('/api/v1/items/search', { params: { by: 'barcode', value }})
          .then(async ({ data }) => {
            await this.$store.commit('products/INDEX', data.products);
            await this.$store.commit('pricing_systems/SET_PRICINGS', { id: 1, pricings: data.pricings });
            await this.$store.commit('ingredients/INGREDIENT_INDEX', data.ingredients);
            this.options = [ ...Object.values(this.variations).filter(v => data.product_ids.includes(v.product_id)), ...data.ingredients ];
          })
          .catch(err => console.log(err));
        }, 50);
        this.loading = false;
      }
    },
    options: function (options) {
      let search = this.search ? this.search.toUpperCase() : this.search,
        results = options.filter(op => op.barcode == search);
        if (results && results.length == 1) {
          this.input = { type: results[0].product_id ? 'product' : 'ingredient', id: results[0].id };
          if (this.clearAfterSelect) setTimeout(() => this.search = null, 50);
        }
    },
  },
}
</script>