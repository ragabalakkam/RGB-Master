<template>
  <div>
    <widget
      v-model="loading"
      :models="versions"
      :title="$t('ال') + $t('versions')"
      :link="{ name: 'versions.create', params: { action: 'create' }, query: { app_id } }"
      :on-created-actions="['versions/index']"
      :not-yet-attr="$t('versions')"
      :attr="$t('XY', { 0: $t('version'), 1: apps[app_id] ? parseName(apps[app_id].name) : $t('app') })"
      permission="versions"
    >
      <b-select
        class="mb-3"
        :data="apps"
        :label="$t('ال') + $t('app')"
        :cast-text="x => parseName(x)"
        :null-option-attr="$t('app')"
        v-model="app_id"
      />

      <flex-table
        v-if="app"
        class="mb-4"
        :data="withoutTrashed(versions).filter(v => v.app_id == app.id)"
        :head="{
          number: $t('X', { 0: $t('number'), 1: $t('version') }),
          path: $t('ال') + $t('path'),
          description: $t('ال') + $t('description'),
          actions: $t('actions'),
        }"
        :classes="{
          number      : wXS ? 'flex-1' : 'wd-140',
          path        : 'flex-2',
          description : 'flex-2',
          actions     : 'wd-150',
        }"
        :casts="{
          description: x => abbr(x, wXS ? 60 : 200),
        }"
        :actions="wXS ? null : [
          { text: $t('download'), variant: 'success', func: ({ id }) => download(id) },
        ]"
        :on-item-clicked="({ id }) => $router.push({ name: 'versions.show', params: { id } })"
        :hidden-xs="['path', 'description']"
        :items-per-page="5"
        permission="versions"
        module="versions"
      />
    </widget>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Widget from "../../../../common/masters/ControlPanel/components/Widget";
import FlexTable from '../../../../common/masters/ControlPanel/components/FlexTable.vue';
export default {
  names: "versions.index",
  data() {
    return {
      app_id: null,
      loading: true,
    };
  },
  created() {
    this.$store.dispatch('apps/index').then(apps => {
      let app = this.withoutTrashed(apps)[0];
      if (app) this.app_id = app.id;
    });
  },
  computed: {
    ...mapGetters({
      apps: 'apps/apps',
      versions: 'versions/versions',
    }),
    app() {
      return this.app_id ? this.apps[this.app_id] : null;
    },
  },
  methods: {
    download: async function (id) {
      this.loading = true;
      await this.$store.dispatch('versions/download', id)
        .then(link => this.downloadURI(link));
      this.loading = false;
    },
  },
  components: {
    Widget,
    FlexTable,
  },
};
</script>
