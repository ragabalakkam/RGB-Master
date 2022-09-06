<template>
  <client-page>
    <div class="mb-4">
      <h4 class="mb-4" v-text="ucFirst($t('yourX', { x: $t('organizations') }))" />

      <div class="row">
        <div class="col-md-4 mb-3 d-flex flex-gap-2">
          <b-input class="flex-1" v-model="search" :placeholder="$t('searchFor', { attr: $t('organization') })" />
          <b-button type="submit" variant="primary" :disabled="!search" icon="search" />
        </div>
        <div class="col-md-8 text-right mb-3">
          <b-router :to="{ name: 'organizations.create', params: { action: 'create' } }" class="btn btn-primary">
            <b-i icon="globe" class="font-300 mr-1" />
            <span v-text="$t('addNewX', { attr: $t('organization') }) + $t('ة')" />
          </b-router>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-4 mb-4" v-for="org in filteredOrganizations" :key="org.id">
        <b-router
          :to="{ name: 'organizations.show', params: { org_id: org.id }}"
          class="d-block bg-light-5 bg-hover-light border border-x rounded-lg p-3 d-flex flex-gap-3"
        >
          <b-img :src="parseImg(org)" />
          <div>
            <p class="fs-5 mb-1" v-text="parseName(org.name)" />
            <p v-text="parseName(org.slogan)" />
          </div>
        </b-router>
      </div>
    </div>
  </client-page>
</template>

<script>
import { mapGetters } from "vuex";
import ClientPage from "../../../../common/masters/Client/ClientPage.vue";
export default {
  name: "client.organizations.index",
  data() {
    return {
      search: null,
    };
  },
  computed: {
    ...mapGetters({
      organizations: "client/organizations",
    }),
    filteredOrganizations() {
      let search = this.search;
      return this.withoutTrashed(this.organizations).filter(org => !search || JSON.stringify(org.name).toLowerCase().includes(search.toLowerCase()))
    },
  },
  components: { ClientPage },
};
</script>