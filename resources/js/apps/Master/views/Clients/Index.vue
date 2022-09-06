<template>
  <widget
    v-model="loading"
    :models="clients"
    :title="$t('ال') + $t('clients')"
    :link="{ name: 'clients.create', params: { action: 'create' } }"
    :on-created-actions="['apps/index', 'clients/index']"
    :not-yet-attr="$t('clients')"
    :attr="$t('client')"
    permission="clients"
  >
    <div class="mb-3 d-flex flex-gap-2">
      <b-button class="py-1 border" :variant="!app_id ? 'primary' : 'light'" v-text="$t('ال') + $t('all')" @click="app_id = null" />
      <b-button class="py-1 border" :variant="app_id == app.id ? 'primary' : 'light'" v-for="app in apps" :key="app.id" v-text="parseName(app.name)" @click="app_id = app.id" />
    </div>

    <flex-table
      :data="withoutTrashed(clients).filter(c => withoutTrashed(c.client_apps).find(a => !app_id || a.app_id == app_id))"
      :head="{
        id                : $t('id'),
        image             : $t('ال') + $t('logo'),
        name              : $t('ال') + $t('name'),
        client_apps       : $t('ال') + $t('apps'),
        tax_number        : $t('theTaxNumber'),
        commercial_reg_no : $t('theCommercialRegNo'),
        phone             : $t('phone'),
        created_at        : $t('sinceX', { x: '' }),
        actions           : $t('actions'),
      }"
      :casts="{
        name              : x => parseName(x),
        created_at        : castTime,
        tax_number        : x => abbr(x, 15, '', false),
        commercial_reg_no : x => abbr(x, 15, '', false),
        phone             : x => abbr(x, 12, ''),
        client_apps       : capps => withoutTrashed(capps).reduce((acc, cur) => acc.concat(`
        <a href='${cur.domain || '#'}' target='_blank'>
          <img
            src='${parseImg(apps[cur.app_id])}'
            title='${parseName(apps[cur.app_id].name)} : ${$t(cur.installed_at ? 'installed' : 'notInstalledYet')}'
            style='height:25px;opacity:${cur.installed_at ? 1 : 0.5};'
          />
        </a>`), []).join(''),
      }"
      :classes="{
        name        : 'flex-2',
        client_apps : 'wd-125',
        actions     : 'wd-125',
      }"
      :actions="[
        {
          variant : 'info',
          icon    : 'eye',
          func    : ({ id }) => $router.push({ name: 'clients.show', params: { id } }),
        },
      ]"
      :hidden-xs="['tax_number', 'commercial_reg_no', 'phone', 'created_at']"
      permission="clients"
      module="clients"
    />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
const FlexTable = () => import('../../../../common/masters/ControlPanel/components/FlexTable.vue');
export default {
  names: "clients.index",
  data() {
    return {
      loading: true,

      app_id: null,
    };
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      apps: 'apps/apps',
      clients: 'clients/clients',
    }),
  },
  components: {
    Widget,
    FlexTable,
  },
};
</script>
