<template>
  <client-page :on-created-actions="['business_types/index']" v-model="loading">
    <template v-if="app">
      <b-img :src="parseImg(app)" size="140" class="mx-auto mb-3" />
      <h4 class="text-center mb-3" v-text="parseName(app.name)" />
      <div class="d-flex-center">
        <b-router class="btn btn-success" :to="user ? { name: 'organization.apps.create', params: { action: 'create' }, query: { app_id: app.id }} : '/client/register'">
          {{ $t('subscribe') }} {{ $t('now') }}
        </b-router>
      </div>

      <hr />

      <p style="white-space: pre-wrap" v-html="parseName(app.description)" />
      
      <hr />

      <div class="row">
        <div v-for="business_type in business_types" :key="business_type.id" class="col-md-4 mb-3">
          <div class="bg-light p-3 rounded-lg h-100">
            <p v-text="parseName(business_type.name)" />
            <p
              class="text-secondary-8 my-3"
              v-text="business_type && business_type.description ? business_type.description : `${$t('thereIsNoX', { x: $t('description') })} ..`"
            />
            <div v-if="business_type" class="d-flex flex-gap-2">
              <div v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" class="bg-white rounded border d-flex flex-column flex-1">
                <p class="border-bottom px-2 py-1 font-md">
                  <b-i :icon="device" class="mr-1" />
                  <span v-t="device" />
                </p>
                <div class="p-1 ht-100 d-flex-center">
                  <img
                    v-if="business_type"
                    v-open-in-viewer
                    class="h-100"
                    :src="`/storage/${business_type.cashier_settings[`${device}_screenshot`]}`"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </client-page>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientPage from '../../../../common/masters/Client/ClientPage.vue';
export default {
  components: { ClientPage },
  names: "apps.show",
  props: {
    id: { default: null },
  },
  data() {
    return {
      tab: 0,
      app: null,
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      all_business_types: 'business_types/business_types',
    }),
    business_types() {
      return this.withoutTrashed(this.all_business_types).filter(type => type.app_id == this.app.id);
    },
  },
  mounted: async function() {
    this.loading = true;
    
    await this.$store
      .dispatch("client/findApp", this.id || this.$route.params.id)
      .then((app) => {
        this.app = app;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "apps.index" }));
      
    this.loading = false;
  },
  methods: {
    openExternally: function (domain) {
      window.open(domain, '_blank').focus()
    },
  },
};
</script>
