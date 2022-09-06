<template>
  <client-page :on-created-actions="['client/fetchApps']">
    <div class="mb-5">
      <h4 class="mb-4" v-text="ucFirst($t('ال') + $t('apps'))" />

      <div class="row">
        <div class="col-md-4 d-flex flex-gap-2">
          <b-input class="flex-1" v-model="search" />
          <b-button type="submit" variant="primary" :disabled="!search" icon="search" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-4 mb-4" v-for="app in filtered_apps" :key="app.id">
        <b-router
          :to="{ name: 'apps.show', params: { id: app.id } }"
          class="d-block bg-light-5 bg-hover-light border border-x rounded-lg p-3"
          :title="parseName(app.description)"
        >
          <div class="d-flex flex-gap-2 align-items-center mb-3">
            <b-img size="30" :src="parseImg(app)" />
            <span class="fs-5 text-primary" v-text="parseName(app.name)" />
          </div>
          <p
            class="mb-3"
            v-text="abbr(parseName(app.description), 200)"
            style="white-space: pre-wrap"
          />
          <div class="d-flex flex-gap-2">
            <b-button class="py-1 px-2" variant="info" icon="arrow-right">{{ $t('exploreX', { x: '' }) }}</b-button>
            <b-router class="btn btn-success py-1 px-2" :to="{ name: 'organization.apps.create', params: { action: 'create' }, query: { app_id: app.id }}">
              {{ $t('subscribe') }}
            </b-router>
          </div>
        </b-router>
      </div>
    </div>
  </client-page>
</template>

<script>
import { mapGetters } from "vuex";
import ClientPage from '../../../../common/masters/Client/ClientPage.vue';
export default {
  name: "apps.index",
  data() {
    return {
      search: null,
    };
  },
  computed: {
    ...mapGetters({
      apps: "client/apps",
    }),
    filtered_apps() {
      let search = this.search;
      return this.withoutTrashed(this.apps).filter(app => !search || JSON.stringify(app.name).toLowerCase().includes(search.toLowerCase()))
    },
  },
  components: { ClientPage },
};
</script>