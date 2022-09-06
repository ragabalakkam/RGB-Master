<template>
  <client-page :on-created-actions="['client/fetchOrganizations', 'client/fetchApps']" class="dashboard p-4">
    <h4 class="mb-5" v-text="$t('dashboard')" />

    <!-- organizations -->
    <section class="mb-5">
      <b-button class="bg-all-none px-0 border-0 mb-2 fs-4" v-text="$t('yourX', { x: $t('organizations') })" icon="users" />
      <div class="row">
        <div v-for="org in withoutTrashed(organizations).splice(0, 3)" :key="org.id" class="col-md-4 mb-2">
          <b-router class="d-block bg-light-5 bg-hover-light border border-x rounded-lg p-3" :to="{ name: 'organizations.show', params: { org_id: org.id } }">
            <div class="d-flex flex-gap-2 align-items-center mb-3">
              <b-img size="40" :src="parseImg(org)" />
              <p class="fs-4" v-text="parseName(org.name)" />
              <p v-text="parseName(org.slogan)" />
            </div>
            <b-button class="bg-info-1 py-1 px-2 fs-3">
              <span v-text="$t('show')" class="mr-1" />
            </b-button>
          </b-router>
        </div>
      </div>
      <div class="d-flex flex-gap-4">
        <b-router :to="{ name: 'organizations.index' }" class="bg-all-none px-0 border-0 mb-2 fs-3">
          <b-i icon="arrow-right" class="mr-1" />
          <span v-text="$t('XY', { 0: $t('show'), 1: $t('allX', { x: $t('yourX', { x: $t('organizations') }) }) })" />
        </b-router>
        <b-router :to="{ name: 'organizations.create', params: { action: 'create'} }" class="text-primary bg-all-none px-0 border-0 mb-2 fs-3">
          <b-i icon="plus" class="mr-1" />
          <span v-text="$t('createX', { attr: $t('organization') })" />
        </b-router>
      </div>
    </section>

    <!-- apps -->
    <section class="mb-5">
      <b-button
        class="bg-all-none px-0 border-0 mb-2 fs-4"
        v-text="$t('XY', { 0: $t('ال') + $t('apps'), 1: $t('ال') + $t('providedByX', { x: $t('XY', { 0: $t('organization'), 1: 'RGB' }) }) })"
        icon="users"
      />
      <div class="row">
        <div v-for="app in withoutTrashed(apps).splice(0, 3)" :key="app.id" class="col-md-4 mb-2">
          <b-router class="d-block bg-light-5 bg-hover-light border border-x rounded-lg p-3" :to="{ name: 'apps.show', params: { id: app.id } }">
            <div class="d-flex flex-gap-2 align-items-center mb-3">
              <b-img size="40" :src="parseImg(app)" />
              <p v-text="parseName(app.name)" />
            </div>
            <p class="fs-3 mb-3" v-text="abbr(parseName(app.description), 150)" />
            <b-button class="bg-info-1 py-1 px-2 fs-3">
              <span v-text="$t('show')" class="mr-1" />
            </b-button>
          </b-router>
        </div>
      </div>
      <div class="d-flex flex-gap-4">
        <b-router :to="{ name: 'apps.index' }" class="bg-all-none px-0 border-0 mb-2 fs-3">
          <b-i icon="arrow-right" class="mr-1" />
          <span v-text="$t('XY', { 0: $t('exploreX', { x: '' }), 1: $t('allX', { x: $t('ال') + $t('apps') }) })" />
        </b-router>
      </div>
    </section>
  </client-page>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientPage from '../../../common/masters/Client/ClientPage.vue';
export default {
  components: { ClientPage },
  name: "home",
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      organizations: 'client/organizations',
      client_apps: 'client/client_apps',
      apps: 'client/apps',
    }),
  },
};
</script>