<template>
  <widget v-model="loading" :title="version ? version.number : null">
    <template v-if="version">

      <!-- uploaded by -->
      <b-form-group v-if="version.user" :label="$t('byX', { x: '' })" class="d-flex align-items-center flex-gap-3 pb-3 mb-3 border-bottom">
        <b-img :src="parseImg(version.user)" size="60" class="rounded-circle" />
        <div class="text-left">
          <p v-text="version.user.name" />
          <small v-text="version.user.email" />
        </div>
      </b-form-group>

      <!-- download button -->
      <div class="text-success c-ptr pb-3 mb-3 border-bottom" @click="download">
        <b-i icon="download" class="mr-2" />
        <span v-text="version.path" dir="ltr" />
      </div>

      <!-- description -->
      <b-form-group v-if="version.description" :label="$t('ال') + $t('description')" class="pb-3 mb-3 border-bottom">
        <p style="white-space: pre-wrap" v-html="version.description" />
      </b-form-group>

      <!-- notes -->
      <b-form-group v-if="version.notes" :label="$t('ال') + $t('notes')" class="pb-3 mb-3 border-bottom">
        <p style="white-space: pre-wrap" v-html="version.notes" />
      </b-form-group>

      <!-- actions -->
      <div class="d-flex flex-gap-2">
        <b-button v-if="version.type == 'patch'" variant="success" class="py-1 px-2" @click="updateAllApps">
          <b-i icon="layer-plus" class="mr-2" />
          <span v-text="$t('updateX', { attr: $t('allX', { x: $t('ال') + $t('apps') }) })" />
        </b-button>
        
        <router-link :to="{ name: 'versions.create', params: { action: 'update', id: version.id }}" class="btn btn-primary py-1 px-2">
          <b-i icon="pen" class="mr-2" />
          <span v-t="'edit'" />
        </router-link>
        
        <b-button variant="danger" class="py-1 px-2" @click.stop="confirmDelete(version, 'versions/delete', version.number)">
          <b-i icon="trash" class="mr-2" />
          <span v-t="'delete'" />
        </b-button>
      </div>
      
    </template>
  </widget>
</template>

<script>
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget");
export default {
  names: "versions.show",
  data() {
    return {
      version: null,
      loading: true,
    };
  },
  mounted() {
    this.$store
      .dispatch("versions/find", this.$route.params.id)
      .then((version) => {
        this.version = version;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "versions.index" }));
  },
  methods: {
    download: async function () {
      this.loading = true;
      await this.$store.dispatch('versions/download', this.version.id)
        .then(link => this.downloadURI(link));
      this.loading = false;
    },
    updateAllApps: async function () {
      this.loading = true;
      await this.$store.dispatch('versions/updateAllApps', this.version.id)
        .then(ids => alert(`${ids.length} apps are now scheduled for update`));
      this.loading = false;
    },
  },
  components: {
    Widget,
  },
};
</script>
