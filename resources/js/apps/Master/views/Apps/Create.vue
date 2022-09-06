<template>
  <create-page
    @submit="$router.push({ name: 'apps.index' })"
    @reset="$router.push({ name: 'apps.index' })"
    @errors="(err) => (errors = err)"
    :errors="errors"
    :title="createOrUpdate('app')"
    :on-created-actions="['apps/index']"
    module="apps"
    v-model="form"
  >
    <!-- image -->
    <b-form-group class="col-12" :label="$t('ال') + $t('logo')">
      <b-img-input v-model="form.image" :errors="errors" name="image" attr="image" class="ht-200" :src="parseImg(form)" />
    </b-form-group>

    <!-- online/offline -->
    <b-on-off-input class="col-12" v-model="form.online">
      {{ $t(form.online ? 'online' : 'offline') }}
    </b-on-off-input>

    <!-- name -->
    <multi-lang-input class="col-12" v-model="form.name" :errors="errors" :name="$t('ال') + $t('name')" attr="name" no-resize />

    <!-- description -->
    <multi-lang-input class="col-12" v-model="form.description" :errors="errors" :name="$t('ال') + $t('description')" attr="description" :rows="5" />

    <!-- configurations -->
    <b-form-group class="col-12" :label="$t('X', { 0: $t('configurations'), 1: $t('app') })">

      <div class="d-flex flex-gap-2 mt-1 mb-3">
        <b-button class="d-flex-center flex-gap-2" variant="primary" @click="addConfigurationGroup">
          <b-i icon="plus-circle" />
          <span v-text="$t('addNewX', { attr: $t('group') }) + $t('ة')" />
        </b-button>
      </div>

      <b-form-group v-for="(configGroup, i) in form.configuration_groups" :key="`group-${i}`">
        <div class="border bg-light p-3">
          
          <!-- config group -->
          <div class="row align-items-end">
            <b-input class="col-md-2" v-model="form.configuration_groups[i].key" :label="$t('key')" />
            <multi-lang-input class="col-md-9"  v-model="form.configuration_groups[i].name" :name="$t('ال') + $t('name')" />
            <!-- <multi-lang-input class="col-md-5"  v-model="form.configuration_groups[i].description" :name="$t('ال') + $t('description')" /> -->
            <b-form-group class="col-md-1 mb-3">
              <b-button variant="outline-danger" class="mt-md-3" block @click="removeConfigurationGroup(i)" v-text="$t('deleteX', { attr: $t('ال') + $t('group') })" />
            </b-form-group>
          </div>

          <table class="table table-bordered tbl-pd-1 bg-white">
            <!-- headers -->
            <tr>
              <td v-text="$t('key')" class="fs-3" />
              <td :colspan="obj_length(locales)" v-text="$t('ال') + $t('name')" class="fs-3" />
              <!-- <td v-text="$t('ال') + $t('description')" /> -->
              <td v-text="$t('ال') + $t('type')" class="fs-3" />
              <td v-text="$t('ال') + $t('default')" class="fs-3" />
              <td class="wd-40" />
            </tr>

            <!-- config group => configs -->
            <tr v-for="(config, j) in configGroup.configurations" :key="`group-${i}-config-${j}`" >
              <td>
                <b-input v-model="form.configuration_groups[i].configurations[j].key" :label="$t('key')" :show-label="false" />
              </td>
              <td :colspan="obj_length(locales)">
                <multi-lang-input v-model="form.configuration_groups[i].configurations[j].name" :name="$t('ال') + $t('name')" :show-labels="false" no-resize />
              </td>
              <!-- <td>
                <multi-lang-input class="col-md-3" v-model="form.configuration_groups[i].configurations[j].description" :name="$t('ال') + $t('description')" />
              </td> -->
              <td>
                <b-select
                  class="flex-1 mb-2 mb-md-0"
                  :data="datatypes"
                  txt="id"
                  :cast-text="$t"
                  :null-option-attr="$t('type')"
                  v-model="form.configuration_groups[i].configurations[j].datatype"
                />
              </td>
              <td>
                <b-input
                  :type="form.configuration_groups[i].configurations[j].datatype"
                  :label="$t('ال') + $t('default')"
                  :show-label="false"
                  v-model="form.configuration_groups[i].configurations[j].default"
                />
              </td>
              <td>
                <b-button variant="danger" block  @click="removeConfiguration(i, j)">
                  <b-i icon="trash-alt" />
                </b-button>
              </td>
            </tr>

            <!-- create new config -->
            <tr>
              <td :colspan="obj_length(locales) + 4">
                <b-button class="bg-info-2" v-text="$t('addNewX', { attr: $t('configurations') }) + $t('ة')" block @click="addConfiguration(i)" />
              </td>
            </tr>
          </table>

        </div>
      </b-form-group>

    </b-form-group>
  </create-page>
</template>

<script>
import { mapGetters } from 'vuex';
import MultiLangInput from '../../../../common/components/Inputs/MultiLangInput.vue';
const CreatePage = () => import("../../../../common/masters/ControlPanel/pages/CreatePage");
export default {
  name: "apps.create",
  props: {
    id    : { default : null },
    action: { default : null },
  },
  data() {
    return {
      form: {
        online: false,
        name: null,
        description: null,
        image: null,
        configuration_groups: [],
      },
      datatypes: [
        { id: 'text' }, 
        { id: 'number' }, 
        { id: 'boolean' }, 
        { id: 'array' }, 
        { id: 'date' },
        { id: 'datetime-local' }, 
        { id: 'email' },
      ],
      errors: {},
    };
  },
  computed: {
    ...mapGetters({
      locales: 'locales/locales',
    }),
    _id() {
      return this.id || this.$route.params.id;
    },
    _action() {
      return this.action || this.$route.params.action || 'create';
    },
  },
  methods: {
    addConfigurationGroup() {
      this.form.configuration_groups.push({
        key: null,
        name: null,
        description: null,
        configurations: [],
      });
    },
    removeConfigurationGroup(index) {
      this.form.configuration_groups.splice(index, 1);
    },
    addConfiguration(group_index) {
      this.form.configuration_groups[group_index].configurations.push({
        datatype: null,
        key: null,
        default: null,
        name: null,
        description: null,
      });
    },
    removeConfiguration(group_index, index) {
      this.form.configuration_groups[group_index].configurations.splice(index, 1);
    },
  },
  components: {
    CreatePage,
    MultiLangInput,
  },
};
</script>