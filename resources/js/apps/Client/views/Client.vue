<template>
  <client-master class="client" :links="sidebar_sections">
    <router-view :key="$route.name" />
  </client-master>
</template>

<script>
import { mapGetters } from 'vuex';
import ClientMaster from '../../../common/masters/Client/ClientMaster.vue';
export default {
  name: "client",
  computed: {
    ...mapGetters({
      apps: 'client/apps',
      client_apps: 'client/client_apps',
      organizations: 'client/organizations',
    }),
    org_id() {
      return this.$route.params.org_id || this.$route.query.org_id;
    },
    isOrganizationRoute() {
      let path = this.$route.fullPath;
      return (path.includes('/organizations/') && !path.includes('/organizations/create')) || path.includes('/organizations-apps/');
    },
    organization() {
      return this.isOrganizationRoute && this.org_id ? this.organizations[this.org_id] : null;
    },
    sidebar_sections() {
      let t = this.$t,
        org = this.organization,
        parseName = this.parseName,
        sections = this.isOrganizationRoute

          // INSIDE AN ORGANIZATION

          ? [
            // Dashboard
            {
              icon: "info-circle",
              text: t('X', { 0: t('information'), 1: t('organization') }),
              to: org ? { name: "organizations.show", params: { org_id: org.id } } : { name: 'organizations.index' },
            },

            // Subscripted Apps
            {
              icon: "window",
              text: t('X', { 0: t("apps"), 1: t('organization') }),
              to: org ? { name: 'organization.apps.index', query: { org_id: org.id } } : { name: 'apps.index' },
            },
          ]

          // PUBLIC

          : [
            // Dashboard
            {
              icon: "tachometer-alt-slow",
              text: t("dashboard"),
              to: { name: "dashboard" },
            },

            // Organizations
            {
              icon: "city",
              text: t('manageX', { attr: t('yourX', { x: t("organizations") })}),
              to: { name: "organizations.index" },
            },

            // Apps
            {
              icon: "window",
              text: t('exploreX', { x: t('ال') + t("apps") }),
              to: { name: "apps.index" },
            },

            // Profile
            {
              icon: "id-badge",
              text: t('profile'),
              to: { name: "profile" },
            },
            
            // Reset password
            {
              icon: "lock",
              text: t('resetPassword'),
              to: { name: "reset_password" },
            },
          ];

      if (this.isOrganizationRoute)
      {
        sections[1].dropdown = [];
        this.withoutTrashed(this.client_apps)
          .filter(ca => ca.client_id == this.org_id)
          .forEach(capp => {
            let app = this.apps[capp.app_id];
            sections[1].dropdown.push({
              text: `${app ? parseName(app.name) : ''} - ${this.abbr(parseName(capp.name), 15)}`,
              to: { name: 'organization.apps.show', params: { id: capp.id }, query: { org_id: this.org_id} },
            })
          });
        sections[1].dropdown.push({
          text: `${t('subscribe')} ${t('inX', { x: t('newX', { x: t('app') }) })}`,
          to: { name: 'organization.apps.create', params: { action: 'create' }, query: { org_id: this.org_id} },
        });
      }
      else
      {
        if (!sections[1].dropdown)
          sections[1].dropdown = [];
        this.withoutTrashed(this.organizations).forEach(org => sections[1].dropdown.push({
          text: parseName(org.name),
          to: { name: 'organizations.show', params: { org_id: org.id }},
        }));
        sections[1].dropdown.push({
          text: t('createX', { attr: t('newX', { x: t('organization') }) }) + t('ة'),
          to: { name: 'organizations.create', params: { action: 'create' } },
        });

        if (!sections[2].dropdown)
          sections[2].dropdown = [];
        this.withoutTrashed(this.apps).forEach(app => sections[2].dropdown.push({
          text: parseName(app.name),
          to: { name: 'apps.show', params: { id: app.id }},
        }));
      }

      return sections;
    },
  },
  components: {
    ClientMaster,
  },
};
</script>