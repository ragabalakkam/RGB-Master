<template>
  <widget
    v-model="loading" :title="app ? $t('XY', { 0: $t('app'), 1: parseName(app.name) }) : null"
    :on-created-actions="['versions/index']"  
  >
    <template v-if="app">
      <multi-tab v-model="tab">

        <!-- about -->
        <tab :name="$t('aboutX', { x: $t('ال') + $t('app')})">
          <b-img :src="parseImg(app)" size="140" class="mx-auto mb-3" />
          <h4 class="text-center" v-text="parseName(app.name)" />
          <hr />
          <p style="white-space: pre-wrap" v-html="parseName(app.description)" />
          <hr />
          <div class="d-flex">
            <router-link :to="{ name: 'apps.create', params: { action: 'update', id: app.id }}" class="btn btn-primary mr-2">
              <b-i icon="pen" class="mr-2" />
              <span v-t="'edit'" />
            </router-link>
            <b-button variant="danger" @click.stop="confirmDelete(app, 'apps/delete')">
              <b-i icon="trash" class="mr-2" />
              <span v-t="'delete'" />
            </b-button>
          </div>
        </tab>

        <!-- clients -->
        <tab :name="$t('ال') + $t('clients')">
          <flex-table
            class="mb-4"
            :data="Object.values(client_apps).filter(capp => capp.app_id == app.id).sort((a, b) => new Date(a.created_at) - new Date(b.created_at))"
            :head="{
              id        : $t('id'),
              name      : $t('ال') + $t('name'),
              domain    : $t('domain'),
              root_dir  : $t('X', { 0: $t('path'), 1: $t('files') }),
              created_at: $t('sinceX', { x: '' }),
              actions   : $t('actions'),
            }"
            :casts="{
              name      : x => parseName(x),
              created_at: castTime,
            }"
            :classes="{
              created_at: 'wd-220',
              actions   : 'wd-220',
            }"
            :actions="[
              {
                variant : 'info',
                icon    : 'eye',
                func    : ({ client_id }) => $router.push({ name: 'clients.show', params: { id: client_id } }),
              },
              {
                variant : 'secondary',
                text    : $t('open'),
                icon    : 'external-link',
                func    : app => openExternally(app.domain),
              },
              {
                variant : app => app.active_process ? ` bg-${app.installed_at ? 'danger' : 'success'}-5 text-light text-hover-white` : app.installed_at ? 'danger' : 'primary',
                text    : app => app.active_process ? `${$t(app.active_process == 'installation' ? 'installing' : 'uninstalling')} ..` : $t(`${app.installed_at ? 'un' : ''}install`),
                func    : app => app.active_process ? null : $store.dispatch('clients/modify_app_installation_status', { id: app.id, value: !!!app.installed_at }),
              },
            ]"
            :hidden-xs="['domain', 'root_dir', 'created_at']"
            :has-update-btn="false"
            :has-delete-btn="false"
          />
        </tab>

        <!-- versions -->
        <tab :name="$t('ال') + $t('versions')">
          <flex-table
            class="mb-4"
            :data="Object.values(versions).filter(version => version.app_id == app.id)"
            :head="{
              number      : $t('ال') + $t('number'),
              description : $t('ال') + $t('description'),
              actions     : $t('actions'),
            }"
            :casts="{
              description : x => abbr(x, 100, ''),
            }"
            :classes="{
              description : 'flex-8',
            }"
            :hidden-xs="['description']"
            :on-item-clicked="({ id }) => $router.push({ name: 'versions.show', params: { id: id } })"
          />
        </tab>

      </multi-tab>
    </template>
  </widget>
</template>

<script>
import Widget from "../../../../common/masters/ControlPanel/components/Widget";
import Tab from '../../../../common/vendor/MultiTab/Tab.vue';
import MultiTab from '../../../../common/vendor/MultiTab/MultiTab.vue';
import FlexTable from '../../../../common/masters/ControlPanel/components/FlexTable.vue';
import { mapGetters } from 'vuex';
export default {
  names: "apps.show",
  data() {
    return {
      tab: 0,
      app: null,
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      client_apps: 'clients/client_apps',
      versions: 'versions/versions',
    }),
  },
  mounted: async function() {
    this.loading = true;
    
    await this.$store
      .dispatch("apps/find", this.$route.params.id)
      .then((app) => {
        this.app = app;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "apps.index" }));

    await axios
      .get(`/api/v1/apps/${this.app.id}/client-apps`)
      .then(({ data }) => this.$store.commit('clients/APP_INDEX', data))
      .catch(errors => console.log(errors));
      
    this.loading = false;
  },
  methods: {
    openExternally: function (domain) {
      window.open(domain, '_blank').focus()
    },
  },
  components: {
    Widget,
    Tab,
    MultiTab,
    FlexTable,
  },
};
</script>
