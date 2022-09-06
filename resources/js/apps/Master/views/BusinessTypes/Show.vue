<template>
  <widget v-model="loading" :title="parseName(type.name)">

    <p v-if="type.description" class="mb-3" v-text="type.description" />

    <b-button variant="success" icon="download" @click="downloadURI(`/storage/${type.zip_path}`, `RGB # ${parseName(type.name)}`)">
      <span v-text="$t('download')" />
      <span class="mx-1">|</span>
      <span dir="ltr" v-text="`RGB # ${parseName(type.name)} .zip`" />
    </b-button>

    <hr>

    <multi-tab v-model="device_index">
      <tab v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" :name="$t(device)">
        <div class="d-flex-center" :style="`height: ${wXS ? '80vh' : '30rem'};`">
          <img
            v-if="type.cashier_settings && type.cashier_settings[`${device}_screenshot`]"
            v-open-in-viewer
            :src="`/storage/${type.cashier_settings[`${device}_screenshot`]}`"
            class="h-100"
          />
          <div v-else class="d-flex-center text-center text-secondary">
            <div>
              <b-i icon="image" size="4x" />
              <p v-text="$t('thereIsNoX', { x: $t('image') })" />
            </div>
          </div>
        </div>
      </tab>
    </multi-tab>

    <hr />

    <div class="d-flex">
      <router-link :to="{ name: 'business_types.create', params: { action: 'update', id: type.id }}" class="btn btn-primary mr-2">
        <b-i icon="pen" class="mr-2" />
        <span v-t="'edit'" />
      </router-link>
      <b-button variant="danger" @click.stop="confirmDelete(type, 'business_types/delete')">
        <b-i icon="trash" class="mr-2" />
        <span v-t="'delete'" />
      </b-button>
    </div>
  </widget>
</template>

<script>
import Widget from '../../../../common/masters/ControlPanel/components/Widget';
import MultiTab from '../../../../common/vendor/MultiTab/MultiTab.vue';
import Tab from '../../../../common/vendor/MultiTab/Tab.vue';
export default {
  names: "types.show",
  data() {
    return {
      type: {},
      device_index: 0,
      loading: true,
    };
  },
  mounted() {
    this.$store
      .dispatch("business_types/find", this.$route.params.id)
      .then((type) => {
        this.type = type;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "business_types.index" }));
  },
  components: {
    Widget,
    MultiTab,
    Tab,
  },
};
</script>
