<template>
  <widget :title="app.live ? '' : $t('dashboard')">
    <p v-if="app.live">
      {{ $t('X', { 0: $t('dashboard'), 1: $t('master')}) }}
    </p>

    <div v-else>
      <div class="d-flex">
        <div class="wd-120 d-flex-center text-center text-success">
          <div>
            <b-i icon="crown" size="4x" class="mb-1" />
            <p class="fs-5">Master</p>
          </div>
        </div>
        <div class="border-left pr-4"></div>
        <div class="flex-1 d-flex flex-gap-3 align-items-center">

          <b-button variant="success" class="p-3" onclick="window.open('http://master.rgbksa.io/@m/create', '_blank').focus()">
            <b-i icon="calendar-plus" size="2x" class="mb-2" />
            <p v-text="$t('newX', { x: $t('version') })" />
          </b-button>
          
          <b-button variant="success" class="p-3" onclick="window.open('http://master.rgbksa.io/@m/update', '_blank').focus()">
            <b-i icon="calendar-edit" size="2x" class="mb-2" />
            <p v-text="$t('updateX', { attr: $t('ال') + $t('version') })" />
          </b-button>

        </div>
      </div>

      <hr />

      <div class="d-flex">
        <div class="wd-120 d-flex-center text-center text-primary">
          <div>
            <b-i icon="browser" size="4x" class="mb-1" />
            <p class="fs-5">RGB</p>
          </div>
        </div>
        <div class="border-left pr-4"></div>
        <div class="flex-1 d-flex flex-gap-3 align-items-center">

          <b-button variant="primary" class="p-3" onclick="window.open('http://afaqstore.io/@m/create', '_blank').focus()">
            <b-i icon="calendar-plus" size="2x" class="mb-2" />
            <p v-text="$t('newX', { x: $t('version') })" />
          </b-button>

          <b-button variant="primary" class="p-3" onclick="window.open('http://afaqstore.io/@m/update', '_blank').focus()">
            <b-i icon="calendar-edit" size="2x" class="mb-2" />
            <p v-text="$t('updateX', { attr: $t('ال') + $t('version') })" />
          </b-button>

          <b-button variant="primary" class="p-3" onclick="window.open('http://afaqstore.io/@m/update-offline-installer', '_blank').focus()">
            <b-i icon="signal-slash" size="2x" class="mb-2" />
            <p v-text="$t('updateX', { attr: $t('XY', { 0: $t('ال') + $t('version'), 1: $t('ال') + $t('offline') })})" />
          </b-button>

          <b-button variant="primary" class="p-3" onclick="window.open('http://afaqstore.io/@m/replace-encoded', '_blank').focus()">
            <b-i icon="redo" size="2x" class="mb-2" />
            <p v-text="'replace encoded'" />
          </b-button>

          <b-button variant="primary" class="p-3" onclick="window.open('http://afaqstore.io', '_blank').focus()">
            <b-i icon="external-link-alt" size="2x" class="mb-2" />
            <p v-t="`open`" />
          </b-button>

        </div>
      </div>
    </div>
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
export default {
  name: "master-dashboard",
  computed: {
    ...mapGetters({
      app: 'configurations/app',
    }),
  },
  components: {
    Widget,
  },
};
</script>