<template>
  <control-panel-master class="master" :links="sidebar_sections">
    <router-view :key="$route.name" />
  </control-panel-master>
</template>

<script>
import ControlPanelMaster from "../../../common/masters/ControlPanel/ControlPanelMaster.vue";
export default {
  name: "master",
  computed: {
    sidebar_sections() {
      let t = this.$t;
      return [
        // Dashboard
        {
          icon: "tachometer-alt-slow",
          text: t("dashboard"),
          to: { name: "dashboard" },
        },

        // Apps
        {
          icon: "computer-classic",
          text: t("ال") + t("apps"),
          to: { name: "apps.index" },
        },

        // Versions
        {
          icon: "layer-group",
          text: t("ال") + t("versions"),
          to: { name: "versions.index" },
        },

        // Business Types
        {
          icon: "user-chart",
          text: t("theBusinessTypes"),
          to: { name: "business_types.index" },
        },

        // Clients
        {
          icon: "users-crown",
          text: t("ال") + t("clients"),
          to: { name: "clients.index" },
        },

        // Locations
        {
          icon: "map",
          text: t("theLocations"),
          dropdown: [
            {
              text: t("ال") + t("countries"),
              to: { name: "locations.countries.index" },
            },
            {
              text: t("ال") + t("states"),
              to: { name: "locations.states.index" },
            },
            {
              text: t("ال") + t("cities"),
              to: { name: "locations.cities.index" },
            },
            {
              text: t("ال") + t("districts"),
              to: { name: "locations.districts.index" },
            },
            // {
            //   text: t("ال") + t("settings"),
            //   to: { name: "locations-settings" },
            // },
          ],
        },

        // TECHNICAL SUPPORT

        {
          text: t("theTechnicalSupport"),
          icon: "headset",
          dropdown: [
            // Call Center
            {
              icon: "headset",
              text: t("callCenter"),
              dropdown: [
                {
                  text: t("voiceCalls"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.voiceCalls" },
                  disabled: true,
                },
                {
                  text: t("videoCalls"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.videoCalls" },
                  disabled: true,
                },
              ],
            },

            // Chat
            {
              icon: "comment-lines",
              text: t("X", { 0: t("service"), 1: t("chat") }),
              dropdown: [
                {
                  text: t("liveChat"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.liveChat" },
                  disabled: true,
                },
                {
                  text: t("chatbotSupport"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.chatbotSupport" },
                  disabled: true,
                },
              ],
            },

            // Self-Service
            {
              icon: "user-md",
              text: t("selfService"),
              dropdown: [
                {
                  text: t("FAQ"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.faq" },
                  disabled: true,
                },
                {
                  text: t("testimonials"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.testimonials" },
                  disabled: true,
                },
              ],
            },

            // Email / Sms
            {
              icon: "envelope-open-text",
              text: t("ال") + t("messaging"),
              dropdown: [
                {
                  text: t("email_service"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.email_service" },
                  disabled: true,
                },
                {
                  text: t("sms_service"),
                  to: { name: 'dashboard' }, // { name: "settings.technicalSupport.sms_service" },
                  disabled: true,
                  // if: this.hasModule('smsServiceUser'),
                },
              ],
            },
          ],
        },

        // MARKETING

        {
          text: t("ال") + t("marketing"),
          icon: "share-alt",
          dropdown: [
            // Loyalty Programs
            {
              icon: "sack-dollar",
              text: t("loyaltyPrograms"),
              dropdown: [
                {
                  text: t("bonusPointsProgram"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.loyaltyPrograms.bonusPointsProgram" },
                  disabled: true,
                },
                {
                  text: t("cashBackRewards"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.loyaltyPrograms.cashBackRewards" },
                  disabled: true,
                },
                {
                  text: t("affiliate"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.loyaltyPrograms.affiliate" },
                  disabled: true,
                },
                {
                  text: t("discountCoupons"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.loyaltyPrograms.discountCoupons" },
                  disabled: true,
                },
              ],
            },

            // Social Media
            {
              icon: "share-alt",
              text: t("socialMedia"),
              dropdown: [
                {
                  text: t("facebook"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.socialMedia.facebook" },
                  disabled: true,
                },
                {
                  text: t("instagram"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.socialMedia.instagram" },
                  disabled: true,
                },
                {
                  text: t("whatsapp"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.socialMedia.whatsapp" },
                  disabled: true,
                },
                {
                  text: t("snapchat"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.socialMedia.snapchat" },
                  disabled: true,
                },
              ],
            },
          ],
        },

        // Monetization

        {
          text: t("monetization"),
          icon: "bullhorn",
          dropdown: [
            // Advertisement
            {
              icon: "bullhorn",
              text: t("advertisement"),
              dropdown: [
                {
                  text: t("sponsors"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.advertisement.sponsors" },
                  disabled: true,
                },
                {
                  text: t("adAreas"),
                  to: { name: 'dashboard' }, // { name: "settings.marketing.advertisement.adAreas" },
                  disabled: true,
                },
              ],
            },
          ],
        },

        // APPEARANCE

        {
          text: t("theAppearance"),
          icon: "palette",
          dropdown: [
            // Locales
            {
              icon: "globe",
              text: t("locales"),
              to: { name: "locales" },
            },
          ],
        },

        // Users
        {
          icon: "book-user",
          text: t("manageX", { attr: t('ال') + t('users') }),
          dropdown: [
            
            // Users
            
            {
              text: t("ال") + t("users"),
              to: { name: "employees.index" },
            },

            // Roles & Permissions
            {
              text: t("ال") + t("roles"),
              to: { name: "roles.index" },
            },
          ],
        },

        // Configurations

        {
          text: t("ال") + t("configurations"),
          icon: "cogs",
          to: { name: "configurations" },
        },
      ];
    },
  },
  components: {
    ControlPanelMaster,
  },
};
</script>