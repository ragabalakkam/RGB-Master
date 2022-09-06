<template>
  <client-page v-if="org">
    <div class="d-flex flex-column-xs align-items-end flex-gap-3">
      <div class="flex-1">
        <b-img size="100" class="rounded-circle mb-4" :src="parseImg(org)" />
        <p class="fs-5 mb-2" v-text="parseName(org.name)" />
        <p class="fs-4 mb-4" v-text="parseName(org.slogan)" />
      </div>
      <div>
        <b-router
          class="btn btn-primary d-flex-center flex-gap-2"
          :to="{ name: 'organizations.create', params: { action: 'update', org_id: org.id } }"
        >
          <b-i icon="user-edit" />
          <p v-text="$t('updateX', { attr: $t('X', { 0: $t('information'), 1: $t('organization') }) })" />
        </b-router>  
      </div>
    </div>

    <hr/>

    <div class="row text-dark-7">
      <div class="col-md-4 mb-4">
        <b-button class="text-left d-flex flex-gap-3 align-items-center h-100" :class="`text-${org.full_address ? 'inherit' : 'danger'}`" block>
          <b-i icon="book-user" class="fs-6 wd-25 text-center" />
          <div>
            <p class="font-md mb-1" v-text="ucFirst($t('address'))" />
            <p class="fs-4 text-dark" v-text="parseName(org.full_address)" />
          </div>
        </b-button>
      </div>
      <div class="col-md-4 mb-4">
        <b-button class="text-left d-flex flex-gap-3 align-items-center h-100" :class="`text-${org.tax_number ? 'inherit' : 'danger'}`" block>
          <b-i icon="book-user" class="fs-6 wd-25 text-center" />
          <div>
            <p class="font-md mb-1" v-text="ucFirst($t('theTaxNumber'))" />
            <p class="fs-4 text-dark" v-text="org.tax_number" />
          </div>
        </b-button>
      </div>
      <div class="col-md-4 mb-4">
        <b-button class="text-left d-flex flex-gap-3 align-items-center h-100" :class="`text-${org.commercial_reg_no ? 'inherit' : 'danger'}`" block>
          <b-i icon="book-user" class="fs-6 wd-25 text-center" />
          <div>
            <p class="font-md mb-1" v-text="ucFirst($t('theCommercialRegNo'))" />
            <p class="fs-4 text-dark" v-text="org.commercial_reg_no" />
          </div>
        </b-button>
      </div>
      <div v-if="org.email" class="col-md-4 mb-4">
        <b-button class="text-inherit text-left d-flex flex-gap-3 align-items-center h-100" @click="visit(`mailto:${org.email}`)" block>
          <b-i icon="mailbox" class="fs-6 wd-25 text-center" />
          <div>
            <p class="font-md mb-1" v-text="ucFirst($t('email'))" />
            <p class="fs-4 text-dark" v-text="org.email" />
          </div>
        </b-button>
      </div>
      <div v-if="org.phone" class="col-md-4 mb-4">
        <b-button class="text-inherit text-left d-flex flex-gap-3 align-items-center h-100" @click="visit(`tel:${org.phone}`)" block>
          <b-i icon="phone" class="fs-6 wd-25 text-center" />
          <div>
            <p class="font-md mb-1" v-text="ucFirst($t('phone'))" />
            <p class="fs-4 text-dark" v-text="org.phone" />
          </div>
        </b-button>
      </div>
    </div>

    <hr/>
    
    <template v-if="obj_length(client_apps)">
      <b-form-group :label="$t('subscribedApps')" label-class="fs-4 my-3">
        <div class="row">
          <div class="col-12 col-md-4 mb-4" v-for="client_app in client_apps" :key="client_app.id">
            <div class="btn btn-light bg-light-5 btn-block text-left border border-x p-4">
              <div v-if="apps[client_app.app_id]" class="d-flex flex-gap-2 align-items-center mb-3">
                <b-img size="30" :src="parseImg(client_app.image ? app : apps[client_app.app_id])" />
                <span class="fs-5 text-primary" v-text="parseName(apps[client_app.app_id].name)" />
              </div>
              <p
                class="mb-3"
                v-text="`${$t('subscribed')} ${castTime(client_app.created_at)}`"
              />
              <div class="d-flex flex-gap-2">
                <div class="d-flex flex-gap-2">
                  <div
                    @click.stop
                    class="btn btn-danger py-1 px-2"
                    v-text="$t('stop')"
                  />
                  <b-router
                    @click.stop
                    class="btn btn-primary py-1 px-2"
                    :to="{ name: 'organization.apps.create', params: { action: 'create' }, query: { org_id, app_id: client_app.app_id } }"
                    v-text="$t('newX', { x: $t('subscribe') })"
                  />
                  <b-router
                    class="btn btn-outline-info text-hover-light py-1 px-2"
                    :to="{ name: 'organization.apps.show', params: { id: client_app.id }, query: { org_id: org.id } }"
                    v-text="$t('showX', { attr: $t('ال') + $t('app') })"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </b-form-group>
      <hr/>
    </template>

    <template v-if="obj_length(unsubscribed_apps)">
      <b-form-group :label="$t('unsubscribedApps')" label-class="fs-4 my-3">
        <div class="row">
          <div class="col-12 col-md-4 mb-4" v-for="app in unsubscribed_apps" :key="app.id">
            <div class="btn btn-light bg-light-5 btn-block text-left border border-x p-4" :title="parseName(app.name)">
              <div class="d-flex flex-gap-2 align-items-center mb-3">
                <b-img size="30" :src="parseImg(app)" />
                <span class="fs-5 text-primary" v-text="parseName(app.name)" />
              </div>
              <div
                class="mb-3"
                style="white-space:pre-wrap"
                v-text="abbr(parseName(app.description), 125)"
              />
              <div class="d-flex flex-gap-2">
                <b-router
                  class="btn btn-primary py-1 px-2"
                  :to="{ name: 'organization.apps.create', params: { action: 'create' }, query: { org_id, app_id: app.id } }"
                  v-text="$t('xForFree', { x: `${$t('subscribe')} ${$t('now')}` })"
                />
                <b-router
                  class="btn btn-outline-info text-hover-light py-1 px-2"
                  :to="{ name: 'apps.show', params: { id: app.id } }"
                  v-text="$t('readMore')"
                />
              </div>
            </div>
          </div>
        </div>
      </b-form-group>
    </template>
  </client-page>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientPage from '../../../../common/masters/Client/ClientPage.vue';
export default {
  name: "organizations.show",
  data() {
    return {
      org: null,
    };
  },
  computed: {
    ...mapGetters({
      apps: 'client/apps',
      all_client_apps: 'client/client_apps',
    }),
    org_id() {
      return this.$route.params.org_id;
    },
    client_apps() {
      return this.withoutTrashed(this.all_client_apps).filter(ca => ca.client_id == this.org_id);
    },
    unsubscribed_apps() {
      return this.withoutTrashed(this.apps).filter(a => !this.client_apps.find(x => x.app_id == a.id));
    },
  },
  mounted() {
    this.$store
      .dispatch("client/findOrganization", this.org_id)
      .then(org => {
        this.org = org;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "organizations.index" }));
    this.$store.dispatch('client/fetchClientApps', this.org_id);
  },
  components: { ClientPage },
};
</script>