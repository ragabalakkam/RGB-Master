<template>
  <div class="position-relative">
    <!-- input -->
    <b-input v-bind="$attrs" v-model="search" list="select-item-by-name" />

    <!-- datalist -->
    <datalist id="select-item-by-name">
      <option v-for="(option, i) in options" :key="i" :value="parseName(option.name)" />
    </datalist>

    <!-- loading -->
    <clip-loader v-show="loading" size="14px" class="text-light position-absolute position-bottom-right mb-1 mr-2" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'select-item-by-name',
  props: {
    value           : { default: () => {} },
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
      variations: 'products/variations',
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
      let match = this.options.find(op => this.parseName(op.name) == value);
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
          await axios.get('/api/v1/items/search', { params: { by: 'name', value }})
          .then(async ({ data }) => {
            await this.$store.commit('products/INDEX', data.products);
            await this.$store.commit('pricing_systems/SET_PRICINGS', { id: 1, pricings: data.pricings });
            await this.$store.commit('ingredients/INGREDIENT_INDEX', data.ingredients);
            this.options = [ ...Object.values(this.variations).filter(v => data.product_ids.includes(v.product_id)), ...data.ingredients ];
          })
          .catch(err => console.log(err));
        }, 150);
        this.loading = false;
      }
    },
  },
}
</script>