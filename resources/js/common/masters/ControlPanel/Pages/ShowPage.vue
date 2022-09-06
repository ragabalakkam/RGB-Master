<template>
  <widget :title="title" :on-created-actions="onCreatedActions" v-model="loading">
    <slot />
    
    <div class="d-flex flex-gap-3 mt-4" v-if="row && user && module">
      <!-- edit button = link to edit page -->
      <router-link :to="{ name: `${route}.create`, params: { action: 'update', id: row.id } }" class="btn btn-primary" size="sm">
        <b-i icon="pen" class="mr-2" />
        <span v-t="'edit'" />
      </router-link>

      <!-- delete button -->
      <b-button variant="danger" @click.stop="confirmDelete(row, `${module}/delete${modulePostfix}`, ...deleteParams)" size="sm">
        <b-i icon="trash" class="mr-2" />
        <span v-t="'delete'" />
      </b-button>
    </div>
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
const Widget = () => import('../components/Widget.vue');
export default {
  name: 'show-page',
  props: {
    value:            { required : false },
    module:           { default : null },
    route:            { default : '' },
    modulePostfix:    { default : '' },
    findParams:       { default : () => {}, type: Object },
    deleteParams:     { default : () => {}, type: Array },
    title:            { default: null },
    onCreatedActions: { default: () => [], type: Array },
  },
  data() {
    return {
      row: null,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
    loading: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
  },
  mounted: function () {
    this.loading = true;
    this.$store.dispatch(`${this.module}/find${this.modulePostfix}`, this.findParams || this.$route.params.id)
      .then(row => {
        this.$emit('fetched', row);
        this.row = row;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: `${this.route}.index` }));
  },
  components: {
    Widget,
  },
}
</script>
