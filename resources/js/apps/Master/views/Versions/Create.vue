<template>
  <create-page
    @submit="$router.push({ name: 'versions.index' })"
    @reset="$router.push({ name: 'versions.index' })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :title="createOrUpdate('version')"
    :on-created-actions="['versions/index', 'apps/index']"
    module="versions"
    v-model="form"
    :disabled="(_action == 'create' && !form.file) || !form.type || !form.app_id"
  >
    <!-- type -->
    <b-form-group class="col-12 d-flex flex-gap-3 flex-column-xs flex-gap-md-bs">
      <b-radio-input
        v-for="option in options"
        :key="option"
        :val="option"
        v-model="form.type"
        class="flex-1 btn  border py-3"
        :class="`btn-${form.type == option ? 'primary' : 'light'}`"
      >
        <p class="mb-2 fs-5" v-text="ucFirst($t(option))" />
        <p v-t="`${option}Description`" />
      </b-radio-input>
    </b-form-group>

    <!-- app -->
    <b-select
      v-if="!_app_id"
      class="col-12"
      :data="apps"
      :null-option-attr="$t('app')"
      :label="$t('ال') + $t('app')"
      :cast-text="x => parseName(x)"
      v-model="form.app_id"
    />

    <!-- file -->
    <b-form-group v-if="_action == 'create'" :label="$t('ال') + $t('file')" class="col-12">
      <!-- <b-file-input v-model="form.file" accept=".zip,.7zip,.rar,.exe" /> -->
      <large-file-uploader ref="largeFileUploader" v-if="_action == 'create'" v-model="form.file" />
    </b-form-group>

    <!-- description -->
    <b-textarea
      rows="10"
      class="col-12"
      :label="$t('ال') + $t('description')"
      v-model="form.description"
      :errors="errors"
      :placeholder="$t('addX', { attr: $t('someX', { x: $t('data') }) }) + ' ..'"
    />

    <!-- notes -->
    <b-textarea
      rows="3"
      class="col-12"
      :label="$t('ال') + $t('notes')"
      v-model="form.notes"
      :errors="errors"
      :placeholder="$t('addX', { attr: $t('someX', { x: $t('data') }) }) + ' ..'"
    />
  </create-page>
</template>

<script>
import { mapGetters } from 'vuex';
const CreatePage = () => import("../../../../common/masters/ControlPanel/pages/CreatePage");
const BSelect = () => import('../../../../common/components/Inputs/Select/BSelect.vue');
import LargeFileUploader from '../../../../common/components/LargeFileUploader.vue';
export default {
  name: "versions.create",
  props: {
    id    : { default : null },
    action: { default : null },
  },
  data() {
    return {
      form: {
        type: 'major',
        file: null,
        description: null,
        app_id: null,
        notes: null,
      },
      errors: {},
      options: ['major', 'minor', 'patch'],
    };
  },
  computed: {
    ...mapGetters({
      apps: 'apps/apps',
    }),
    _id() {
      return this.id || this.$route.params.id;
    },
    _action() {
      return this.action || this.$route.params.action || 'create';
    },
    _app_id() {
      const id = this.$route.query.app_id;
      if (id) this.form.app_id = id;
      return id;
    },
  },
  components: {
    CreatePage,
    BSelect,
    LargeFileUploader,
  },
};
</script>