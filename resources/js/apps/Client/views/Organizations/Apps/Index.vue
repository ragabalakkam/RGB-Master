<template>
  <client-page>
    <div class="mb-5">
      <h4 class="mb-4" v-text="ucFirst($t('X', { 0: $t('apps'), 1: $t('organization') }))" />

      
      <div class="row">
        <div class="col-md-4">
          <form class="d-flex flex-gap-2" @submit.prevent>
            <b-input v-model="search" />
            <b-button type="submit" variant="primary" :disabled="!search" icon="search" />
          </form>
        </div>
      </div>
     
    </div>

    <div class="row">
      <div class="col-12 col-md-4 mb-4" v-for="client_app in withoutTrashed(client_apps).filter(ca => ca.client_id == org_id)" :key="client_app.id">
        <b-router
          :to="{ name: 'organization.apps.show', params: { app_id: client_app.id }, query: { org_id } }"
          class="btn btn-light btn-block text-left border px-4 py-3"
        >
          <div class="d-flex flex-gap-2 align-items-center">
            <b-img size="30" :src="parseImg(client_app.image ? app : apps[client_app.app_id])" />
            <div>
              <p class="fs-5 text-primary" v-text="parseName(client_app.name)" />
              <p class="fs-3 text-primary" v-text="$t('XY', { 0: $t('app'), 1: parseName(apps[client_app.app_id].name) })" />
            </div>
          </div>
        </b-router>
      </div>
    </div>
  </client-page>
</template>

<script>
import { mapGetters } from "vuex";
import ClientPage from '../../../../../common/masters/Client/ClientPage.vue';
export default {
  name: "organization.apps.index",
  data() {
    return {
      search: null,
    };
  },
  computed: {
    ...mapGetters({
      apps: "client/apps",
      client_apps: 'client/client_apps',
    }),
    org_id() {
      return this.$route.query.org_id;
    },
  },
  created: function () {
    this.$store.dispatch('client/fetchClientApps', this.org_id);
  },
  components: { ClientPage },
};
</script>