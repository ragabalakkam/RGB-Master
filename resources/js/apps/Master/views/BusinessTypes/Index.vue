<template>
  <div>
    <!-- business types -->
    <widget
      :title="$t('theBusinessTypes')"
      :link="{ name: 'business_types.create', params: { action: 'create' } }"
      :on-created-actions="['business_types/index']"
      :models="business_types"
      v-model="loading"
      :not-yet-attr="$t('businessTypes')"
      :attr="$t('businessType')"
    >
      <flex-table
        :data="business_types"
        :head="{
          id: $t('id'),
          name: $t('ال') + $t('name'),
          actions: $t('actions'),
        }"
        :casts="{
          name: name => parseName(name),
        }"
        :classes="{
          actions : 'wd-150',
        }"
        :actions="[
          { text: $t('download'), func: ({ zip_path, name }) => downloadURI(`/storage/${zip_path}`, `RGB # ${parseName(name)}.zip`), variant: 'success' }, 
        ]"
        :on-item-clicked="({ id }) => $router.push({ name: 'business_types.show', params: { id } })"
        module="business_types"
      />
    </widget>

    <!-- import from file -->
    <widget v-if="!loading" :title="`${$t('import')} ${$t('fromX', { attr: $t('file') })}`">
      <b-form @submit="importType" @reset="import_form.file = null" :show-buttons="import_form.file" :submit-text="$t('import')">
        <!-- <b-on-off-input v-model="import_form.same_name" class="col-12">
          إستيراد بنفس الإسم
        </b-on-off-input> -->
        <name-input v-if="!import_form.same_name" class="col-12" v-model="import_form.name" />
        <b-form-group class="col-12">
          <b-file-input v-model="import_form.file" accept=".zip" />
        </b-form-group>
      </b-form>
    </widget>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../../common/masters/ControlPanel/components/Widget.vue");
const FlexTable = () => import('../../../../common/masters/ControlPanel/components/FlexTable.vue');
const NameInput = () => import('../../../../common/components/Inputs/NameInput.vue');
export default {
  names: "business_types.index",
  data() {
    return {
      loading: false,
      errors: {},
      import_form: {
        same_name: false,
        name: null,
        file: null,
      },
    };
  },
  computed: {
    ...mapGetters({
      business_types: "business_types/business_types",
    }),
  },
  methods: {
    importType: async function (id) {
      this.loading = true;
      await this.$store.dispatch('business_types/import', this.import_form)
        .then(() => this.import_form = { same_name: false, name: null, file: null })
        .catch(err => this.errors = err)
      this.loading = false;
    },
  },
  components: {
    Widget,
    FlexTable,
    NameInput,
  },
};
</script>