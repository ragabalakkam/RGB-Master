<template>
  <widget
    v-model="loading"
    :title="createOrUpdate('activity', _action)"
    :on-created-actions="['apps/index']"
  >
    <b-form @submit="submit" @reset="cancel" :submit-text="$t(_action)">

      <!-- app -->
      <b-select
        class="col-12"
        :data="apps"
        :null-option-attr="$t('app')"
        :label="$t('ال') + $t('app')"
        :cast-text="x => parseName(x)"
        v-model="form.app_id"
      />
      
      <!-- name -->
      <name-input class="col-12" v-model="form.name" :errors="errors" />

      <!-- description -->
      <b-textarea
        rows="3"
        class="col-12"
        :label="optional($t('ال') + $t('description'))"
        v-model="form.description"
        :errors="errors"
        :placeholder="$t('addX', { attr: $t('someX', { x: $t('data') }) }) + ' ..'"
      />

      <div class="col-12 mt-2 mb-1"><hr /></div>

      <multi-tab v-if="form.app_id" class="col-12" v-model="tab">

        <!-- cashier settings -->
        <tab :name="$t('X', { 0: $t('screen'), 1: $t('cashier') })">

          <div class="font-md mb-3">
            <b-i icon="info-circle" class="mr-1" />
            <span v-t="'clickToUpdateBusinessTypeScreenshot'" />
          </div>

          <!-- products grid -->
          <div class="row">
            
            <div class="col-md-4 mb-4" v-for="(s, i) in ['desktop', 'tablet', 'mobile']" :key="i">

              <div class="mb-3">
                <!-- rows & cols -->
                <div class="fs-4 p-2 bg-light rounded-top border d-flex align-items-center">
                  <b-i class="mr-2" :icon="s" />
                  <span class="flex-1 fs-4" v-text="ucFirst($t(s))" />

                  <!-- live preview -->
                  <b-button size="sm" class="d-flex-center flex-gap-2 py-1 px-2" @click="preview_device = s">
                    <b-i icon="eye" />
                    <span v-text="ucFirst($t('livePreview'))" />
                  </b-button>
                </div>

                <!-- screenshot -->
                <div class="ht-180 d-flex-center border-left border-right p-1 border-bottom rounded-bottom">
                  <img
                    v-if="screenshots[s] || form.cashier_settings[`${s}_screenshot`]"
                    :src="screenshots[s]|| `/storage/${form.cashier_settings[`${s}_screenshot`]}`"
                    v-open-in-viewer
                    class="h-100"
                  />
                </div>
              </div>
              
              <!-- on / off -->
              <b-on-off-input v-model="form.cashier_settings[`${s}_has_grid`]">
                {{ $t('enable') }}
              </b-on-off-input>

              <template v-if="form.cashier_settings[`${s}_has_grid`]">
                <div v-if="s == 'desktop'" class="d-flex flex-gap-2">
                  <b-input
                    class="flex-2"
                    v-model="form.cashier_settings.desktop_grid_width"
                    :label="`${$t('width')} ${$t('productsGridView')}`"
                  />
                  <b-input
                    class="flex-1"
                    v-model="form.cashier_settings.desktop_grid_gap"
                    :label="$t('theGap')"
                  />
                </div>

                <!-- products grid -->
                <b-form-group :label="$t('productsGridView')">
                  <div class="d-flex-center flex-gap-3 ht-form-control border rounded-lg px-2 text-gray-6">
                    <!--  -->
                    <div class="flex-1 d-flex-center flex-gap-2">
                      <b-input
                        type="number"
                        v-model="form.cashier_settings[`${s}_rows`]"
                        input-class="text-center border-0 py-0 h-100"
                        class="flex-1 ht-25"
                        min="0"
                      />
                      <p v-t="'rows'" class="flex-1 fs-3 text-gray-6 font-bold" />
                    </div>
                    <!--  -->
                    <b-i icon="times" />
                    <!--  -->
                    <div class="flex-1 d-flex-center flex-gap-2">
                      <b-input
                        type="number"
                        v-model="form.cashier_settings[`${s}_cols`]"
                        input-class="text-center border-0 py-0 h-100"
                        class="flex-1 ht-25"
                        min="0"
                      />
                      <p v-t="'cols'" class="flex-1 fs-3 text-gray-6 font-bold" />
                    </div>
                  </div>
                </b-form-group>

                <!-- main categories cols -->
                <b-input
                  type="number"
                  min="0"
                  :max="form.cashier_settings[`${s}_cols`] - form.cashier_settings[`${s}_sub_categories_cols`]"
                  step="1"
                  v-model="form.cashier_settings[`${s}_main_categories_cols`]"
                  :label="$t('xColsCount', { x: $t('theMainCategories') })"
                />

                <!-- sub categories cols -->
                <b-input
                  type="number"
                  min="0"
                  :max="form.cashier_settings[`${s}_cols`] - form.cashier_settings[`${s}_main_categories_cols`]"
                  step="1"
                  v-model="form.cashier_settings[`${s}_sub_categories_cols`]"
                  :label="$t('xColsCount', { x: $t('theSubCategories') })"
                />
              </template>

            </div>
          </div>

          <hr class="mt-0" />

          <!-- colors -->
          <template v-if="form.cashier_settings.mobile_has_grid || form.cashier_settings.tablet_has_grid || form.cashier_settings.desktop_has_grid">
            <div>
              <p class="fs-4 mb-3">
                <b-i icon="palette" class="mr-1" />
                <span v-text="ucFirst($t('ال') + $t('colors'))" />
              </p>
              <div class="row">
                <div class="d-flex flex-gap-2 align-items-center col-md-3 mb-3" v-for="(label, name) in colors" :key="name">
                  <div class="form-control p-0 border-secondary-3 sz-30 rounded-circle overflow-hidden">
                    <input type="color" :id="`color_${name}`" rgba class="sz-50 c-ptr" style="margin: -10px" v-model="form.cashier_settings[name]" />
                  </div>
                  <label :for="`color_${name}`" class="flex-1 c-ptr" v-text="ucFirst(label)" />
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-center">
                  <b-input class="wd-80 mr-2" type="range" min="0" max="1" step="0.01" v-model="form.cashier_settings.product_overlay_transparency" />
                  <span v-text="ucFirst($t('overlayTransparency'))" />
                </div>
              </div>
            </div>

            <hr class="mt-0" />
          </template>

          <!-- inputs -->
          <div>
            <p class="fs-4 mb-3">
              <b-i icon="send-back" class="mr-1" />
              <span v-text="ucFirst($t('ال') + $t('inputs'))" />
            </p>
            <div class="row" v-if="form.cashier_settings.desktop_headers">
              <b-input
                class="col-md-4"
                type="number"
                v-model="form.cashier_settings.desktop_fields_in_row"
                :label="$t('xPerRow', { x: $t('X', { 0: $t('count'), 1: $t('inputs') }) })"
              />
              <b-input
                class="col-md-4"
                type="number"
                v-model="form.cashier_settings.desktop_delivery_items_in_row"
                :label="$t('xPerRow', { x: $t('xOfy', { x: $t('count'), y: $t('theDeliveryCompanies') }) })"
              />
              <b-input
                class="col-md-4"
                type="number"
                v-model="form.cashier_settings.desktop_dineIn_items_in_row"
                :label="$t('xPerRow', { x: $t('X', { 0: $t('count'), 1: $t('tables') }) })"
              />
              <b-checkbox class="col-12 fs-3" v-model="form.cashier_settings.desktop_detailed_items_table">
                {{ ucFirst($t('detailedX', { x: $t('X', { 0: $t('tbl'), 1: $t('products') }) })) }}
              </b-checkbox>
              <b-checkbox class="col-12 fs-3" v-model="form.cashier_settings.desktop_detailed_summary">
                {{ ucFirst($t('detailedX', { x: $t('X', { 0: $t('summary'), 1: $t('invoice') }) })) }}
              </b-checkbox>
              <b-checkbox class="col-12 fs-3" v-model="form.cashier_settings.desktop_headers.show_labels">
                {{ ucFirst($t('showX', { attr: $t('inputLabels') })) }}
              </b-checkbox>

              <div class="col-12"><hr class="mt-0" /></div>

              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.customer">
                {{ $t('selectX', { attr: $t('ال') + $t('customer') }) }}
              </b-checkbox>
              <b-checkbox v-if="form.modules.smsServiceUser" class="col-md-3" v-model="form.cashier_settings.desktop_headers.sms">
                {{ $t('sendX', { x: $t('sms') }) }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.delivery_datetime">
                {{ $t('timestampX', { attr: $t('ال') + $t('receipt') }) }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.warehouse">
                {{ $t('selectX', { attr: $t('ال') + $t('warehouse') }) }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.search">
                {{ $t('ال') + $t('search') }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.select_product">
                {{ $t('selectX', { attr: $t('ال') + $t('product') }) }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.add_by_barcode">
                {{ $t('addX', { attr: `${$t('item')} ${$t('byX', { x: $t('ال') +  $t('barcode') })}` }) }}
              </b-checkbox>
              <b-checkbox class="col-md-3" v-model="form.cashier_settings.desktop_headers.notes">
                {{ $t('X', { 0: $t('notes'), 1: $t('invoice') }) }}
              </b-checkbox>
            </div>
          </div>
          
        </tab>

        <!-- sales_systems -->
        <tab :name="$t('theSalesSystems')" v-if="form.sales_systems && obj_length(form.sales_systems)">
          <table class="table table-borderless mt-1 border">
            <tr class="bg-light fs-3">
              <td v-text="ucFirst($t('enable'))" />
              <td v-for="locale in locales" :key="locale.id" v-text="ucFirst($t('XInLocaleY', { x: $t('ال') + $t('name'), y: $t(locale.label) }))" />
              <td v-text="ucFirst($t('ال') + $t('icon'))" />
            </tr>
            <tr v-for="(sys, i) in Object.values(form.sales_systems)" :key="`sales-system-${i}`" class="border-top">
              <td>
                <b-checkbox class="my-2" v-model="form.modules[`${sys.key}Cashier`]" />
              </td>
              <td v-for="locale in locales" :key="locale.id">
                <b-input :disabled="!form.modules[`${sys.key}Cashier`]" v-if="form.sales_systems[i]" v-model="form.sales_systems[i].name[locale.label]" />
              </td>
              <td v-if="form.sales_systems[i]">
                <div class="d-flex flex-gap-2">
                  <b-input :disabled="!form.modules[`${sys.key}Cashier`]" class="flex-1" v-model="form.sales_systems[i].icon" />
                  <div class="bg-light p-2 rounded border">
                    <b-i :icon="form.sales_systems[i].icon" />
                  </div>
                </div>
              </td>
            </tr>
          </table>
          
          <hr />

          <!-- table images -->
          <div v-if="form.modules.dineInCashier">
            <p class="fs-4 mb-3">
              <b-i icon="grip-horizontal" class="mr-1" />
              <span v-text="ucFirst($t('X', { 0: $t('images'), 1: parseName(form.sales_systems[1].name) || $t('tables') }))" />
            </p>
            <div class="row">
              <b-form-group :label="$t(name) + $t('ة')" class="col-6 col-md-2 mb-3" v-for="name in ['idle', 'busy', 'reserved']" :key="name">
                <b-img-input
                  class="ht-160"
                  :src="`/storage/${form.cashier_settings[`${name}_img`]}`"
                  v-model="table_imgs[name]"
                />
              </b-form-group>
            </div>
          </div>
        </tab>

        <!-- modules -->
        <tab :name="$t('X', { 0: $t('modules'), 1: $t('client') })">
          <b-input v-model="search" :placeholder="$t('searchByX', { x: $t('X', [$t('name'), $t('module')]) })" />

          <hr>

          <div class="row">
            <div class="col-md-4" v-for="mod in filtered_mods" :key="mod.key">
              <b-on-off-input v-model="form.modules[mod.key]" size="lg">
                <p v-text="ucFirst(parseName(mod.name))" />
              </b-on-off-input>
              <hr class="my-2" />
            </div>
          </div>
        </tab>

        <!-- translations -->
        <tab :name="$t('ال') + $t('translations')">
          <table class="table table-borderless mt-1 border">
            <tr class="bg-light fs-3">
              <td v-t="'key'" />
              <td v-for="locale in locales" :key="locale.id" v-text="$t('XInLocaleY', { x: $t('ال') + $t('value'), y: $t(locale.label) })" />
            </tr>
            <tr v-for="key in translations" :key="`translations-${key}`" class="border-top">
              <td class="bg-light border-right">
                <p v-text="key" class="p-2" />
              </td>
              <td v-for="locale in locales" :key="locale.id" >
                <b-input v-if="form.translations[key]" v-model="form.translations[key][locale.label]" />
              </td>
            </tr>
          </table>
        </tab>
        
        <!-- extra data -->
        <tab :name="$t('extraX', { x: $t('forms') }) + $t('ة')">
          <b-button v-if="form.forms.length" class="mb-3" size="sm" variant="primary" @click="addNewForm">
            <b-i icon="plus" />
            <span v-text="$t('createNewX', { attr: $t('form') }) + $t('ة')" />
          </b-button>

          <div class="text-secondary py-2" v-else>
            <span v-text="`${$t('thereIsNoX', { x: $t('forms') })}.`" />
            <span
              class="text-primary text-hover-info c-ptr"
              v-text="$t('wantToAddX', { attr: $t('newX', { x: $t('extraX', { x: $t('form') }) + $t('ة') }) + $t('ة') })"
              @click="addNewForm"
            />
          </div>

          <!-- forms -->
          <b-form-group v-for="(newForm, i) in form.forms" :key="`form-${i}`" class="rounded border bg-light overflow-hidden">
            <div class="bg-info-2 d-flex flex-gap-3 align-items-center">
              <!-- de / activate form -->
              <b-on-off-input class="ml-3 mb-0" size="lg" v-model="newForm.active">
                {{ $t('active') }}
              </b-on-off-input>
              
              <!-- title -->
              <p class="flex-1" />

              <template v-if="form.forms.length > 1">
                <!-- order - up/- -->
                <b-button @click="changeFormOrder(i, -1)" class="bg-all-none text-secondary-5 border-0">
                  <b-i icon="chevron-up" />
                </b-button>

                <!-- order - down/+ -->
                <b-button @click="changeFormOrder(i, 1)" class="bg-all-none text-secondary-5 border-0">
                  <b-i icon="chevron-down" />
                </b-button>
              </template>

              <!-- delete from -->
              <b-button @click="removeForm(i)" class="bg-all-none text-secondary-5 border-0">
                <b-i icon="times" />
              </b-button>
            </div>

            <!-- form -->
            <div class="row mx-0 mt-3">
              <name-input class="col-md-12 mb-3" v-model="newForm.name" />

              <div v-if="false" class="col-md-3 d-flex flex-gap-2 align-items-center">
                <div class="form-control p-0 border-secondary-3 sz-30 rounded-circle overflow-hidden">
                  <input type="color" :id="`${i}-bg`" rgba class="sz-50 c-ptr" style="margin: -10px" v-model="newForm.bgColor" />
                </div>
                <label :for="`${i}-bg`" class="flex-1 c-ptr mb-0" v-text="ucFirst($t('backgroundColor'))" />
              </div>

              <div v-if="false" class="col-md-3 d-flex flex-gap-2 align-items-center">
                <div class="form-control p-0 border-secondary-3 sz-30 rounded-circle overflow-hidden">
                  <input type="color" :id="`${i}-fg`" rgba class="sz-50 c-ptr" style="margin: -10px" v-model="newForm.fgColor" />
                </div>
                <label :for="`${i}-fg`" class="flex-1 c-ptr mb-0" v-text="ucFirst($t('foregroundColor'))" />
              </div>

              <b-select class="col-md-4" :data="dirs" v-model="newForm.dir" txt="id" :cast-text="$t" :label="$t('dir')" />
              <b-input class="col-md-4" v-model="newForm.width" :label="$t('ال') + $t('width')" />
              <icon-input class="col-md-4" :label="$t('ال') + $t('icon')" overlay-class="mr-3 mb-3" v-model="newForm.icon" />

              <b-on-off-input class="col-12 mb-2" v-model="newForm.show_in_sales">
                {{ $t('appearInX', { x: $t('ال') + $t('sales') }) }}
              </b-on-off-input>
              <b-on-off-input class="col-12 mb-2" v-model="newForm.show_in_purchases">
                {{ $t('appearInX', { x: $t('ال') + $t('purchases') }) }}
              </b-on-off-input>
              <b-on-off-input class="col-12" v-model="newForm.show_in_forms">
                {{ $t('appearInX', { x: $t('X', { 0: $t('forms'), 1: `${$t('customers')} / ${$t('suppliers')}` }) }) }}
              </b-on-off-input>

              <!-- fields -->
              <b-form-group class="col-12 p-2 p-md-3 border-top mt-2" :label="$t('ال') + $t('inputs')">
                <div
                  v-for="(field, j) in newForm.fields"
                  :key="`field-${i}-${j}`"
                  class="d-flex flex-column-xs flex-gap-2 p-2 border-bottom"
                  :class="`bg-${field.active ? 'white' : 'light'}`"
                >
                  <b-form-group class="wd-30 d-flex flex-column mb-2 mb-md-0">
                    <b-button class="flex-1 p-0 d-flex-center border fa-signal-3" @click="changeFieldOrder(i, j, -1)">
                      <b-i icon="chevron-up" />
                    </b-button>
                    <b-button class="flex-1 p-0 d-flex-center border fa-signal-3" @click="changeFieldOrder(i, j, 1)">
                      <b-i icon="chevron-down" />
                    </b-button>
                  </b-form-group>
                  <name-input class="flex-4 mb-2 mb-md-0" :gap="2" v-model="field.name" />
                  <b-select class="flex-1 mb-2 mb-md-0" :data="datatypes" :cast-text="$t" txt="id" v-model="field.datatype" :label="$t('ال') + $t('type')" />
                  <b-input class="flex-1 mb-2 mb-md-0" :label="`${$t('ال') + $t('size')} / 12`" v-model="field.cols" />
                  <icon-input v-if="false" class="flex-2 mb-2 mb-md-0" :label="$t('ال') + $t('icon')" v-model="field.icon" :show-link="false" />
                  <b-select class="flex-1" :data="dirs" v-model="field.dir" txt="id" :cast-text="$t" :label="$t('dir')" />
                  <b-input class="flex-1 mb-2 mb-md-0" :label="'class'" v-model="field.class" />
                  <b-input class="flex-1 mb-2 mb-md-0" :label="'style'" v-model="field.style" />
                  <b-on-off-input class="mb-2 mb-md-0" size="xl mt-2" v-model="field.required" :label="$t('required')" />
                  <b-on-off-input class="mb-2 mb-md-0" size="xl mt-2" v-model="field.active" :label="$t('active')" />
                  <b-button variant="outline-danger" class="my-2 px-2" @click="removeFormField(i, j)">
                    <b-i icon="trash-alt" />
                  </b-button>
                </div>
                
                <b-button
                  variant="info"
                  @click="addNewFormField(i)"
                  v-text="$t('createNewX', { attr: $t('field') })"
                  :disabled="newForm.fields.find(f => !f.name || !f.name.en || !f.name.ar)"
                  block
                />
              </b-form-group>
            </div>
          </b-form-group>
        </tab>

        <!-- database -->
        <tab :name="$t('theDatabase')">
          <b-radio-input
            v-for="(text, option) in databaseOptions"
            :key="option"
            :val="option"
            v-model="form.database.option"
            class="mb-2 pt-2 border-top"
          >
            <p :class="{'text-primary' : form.database.option == option }">
              <b-i :fas="form.database.option == option ? 'fas' : 'far'" icon="circle" />
              <span class="ml-1" v-text="ucFirst(text)" />
            </p>

            <!-- client -->
            <b-form-group v-if="option == 'client' && form.database.option == 'client'">
              <b-select
                v-if="!loading_dbs"
                :data="client_dbs"
                :disabled="form.database.option != 'client'"
                :null-option-attr="$t('X', { 0: $t('database'), 1: $t('client') })"
                :cast-text="(name, backup) => `${name} - ${castTime(backup.created_at)}`"
                val="path"
                v-model="form.database.file"
              />
              <div v-else class="form-control bg-light d-flex flex-gap-2 text-secondary-7">
                <clip-loader size="14px" class="mt-1" />
                <p v-text="`${$t('pleaseWait')} ..`" />
              </div>
            </b-form-group>

            <!-- file -->
            <b-form-group v-else-if="option == 'file' && form.database.option == 'file'">
              <b-file-input
                :disabled="form.database.option != 'file'"
                :class="{ 'is-invalid': errors.database_file }"
                accept=".sql"
                v-model="form.database.file"
              />
              <b-error :field="errors.database_file" :attr="$t('xOfy', { x: $t('file'), y: $t('database') })" />
            </b-form-group>
          </b-radio-input>
        </tab>

      </multi-tab>
    </b-form>

    <div v-if="preview_device" class="position-absolute position-top-left h-100 w-100 bg-dark-4 index-up d-flex-center" @click="preview_device = null">
      <div id="products-grid" class="p-3 bg-white">
        <products-grid
          :settings="form.cashier_settings"
          :device="preview_device"
          :style="{
            'width': `${wXS ? '100%' : preview_device == 'mobile' ? form.cashier_settings.mobile_has_grid ? '40vw' : '20vw' : preview_device == 'tablet' ? '60vw' : '80vw'}`,
          }"
        />
      </div>
    </div>
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
import Widget from '../../../../common/masters/ControlPanel/components/Widget.vue';
import MultiTab from '../../../../common/vendor/MultiTab/MultiTab.vue';
import Tab from '../../../../common/vendor/MultiTab/Tab.vue';
import NameInput from '../../../../common/components/Inputs/NameInput.vue';
import IconInput from '../../../../common/components/Inputs/IconInput.vue';
import BSelect from '../../../../common/components/Inputs/Select/BSelect.vue';
import ProductsGrid from './ProductsGrid.vue';
export default {
  name: 'business_types.create',
  data() {
    return {
      tab: 0,
      form: {
        app_id: null,
        name: null,
        description: null,
        cashier_settings: {},
        modules: {},
        sales_systems: [],
        translations: {},
        forms: [],
        database: {
          option: 'none',
          file: null,
        },
      },
      translations: [
        'diningArea',
        'diningAreas',
        'diningTable',
        'diningTables',
        'routes.dining_areas.index',
        'routes.dining_areas.create',
        'routes.dining_areas.show',
        'routes.dining_tables.index',
        'routes.dining_tables.create',
        'routes.dining_tables.show',
        'table',
        'tables',
        'cashier',
        'moveOrderToAnotherTable',
      ],
      colors: {
        main_category_bg          : this.$t("xBgColor", { x: this.$t("theMainCategories") }),
        main_category_fg          : this.$t("xFgColor", { x: this.$t("theMainCategories") }),
        selected_main_category_bg : this.$t("xBgColor", { x: this.$t("selectedX", { x: this.$t("theMainCategory") }) + this.$t("ة") }),
        selected_main_category_fg : this.$t("xFgColor", { x: this.$t("selectedX", { x: this.$t("theMainCategory") }) + this.$t("ة") }),

        sub_category_bg           : this.$t("xBgColor", { x: this.$t("theSubCategories") }),
        sub_category_fg           : this.$t("xFgColor", { x: this.$t("theSubCategories") }),
        selected_sub_category_bg  : this.$t("xBgColor", { x: this.$t("selectedX", { x: this.$t("theSubCategory") }) + this.$t("ة") }),
        selected_sub_category_fg  : this.$t("xFgColor", { x: this.$t("selectedX", { x: this.$t("theSubCategory") }) + this.$t("ة") }),

        product_bg                : this.$t("xBgColor", { x: this.$t("ال") + this.$t("products") }),
        product_overlay_bg        : this.$t("xBgColor", { x: this.$t("xOverlay", { x: this.$t("ال") + this.$t("products") }) }),
        product_overlay_fg        : this.$t("xFgColor", { x: this.$t("xOverlay", { x: this.$t("ال") + this.$t("products") }) }),
      },
      table_imgs: {
        busy: null,
        idle: null,
        reserved: null,
      },
      datatypes: [
        { id: 'text'}, 
        { id: 'number'}, 
        // { id: 'boolean'}, 
        // { id: 'array'}, 
        { id: 'date'},
        { id: 'datetime-local'}, 
        { id: 'email'},
      ],
      errors: {},

      dirs: [{ id: 'auto' }, { id: 'ltr' }, { id: 'rtl' }],
      search: null,

      loading_dbs: false,
      client_dbs: [],

      preview_device: false,
      screenshots: {
        desktop: null,
        tablet: null,
        mobile: null,
      },

      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      apps: 'apps/apps',
      locales: 'locales/locales',
      modules: "configurations/modules",
      settings: "configurations/cashier_settings",
    }),
    _id() {
      return this.$route.params.id;
    },
    _action() {
      return this.$route.params.action;
    },
    app() {
      let app_id = this.form.app_id;
      return app_id ? this.apps[app_id] : null;
    },
    filtered_mods() {
      let app = this.app;
      if (!app) return [];

      let modules = app.configuration_groups.find(group => group.key == 'modules');
      if (!modules) return [];
      else modules = modules.configurations;

      let s = this.search;
      if (s) s = s.toLowerCase();

      return Object.values(modules).filter(mod => !s || JSON.stringify(mod.name).toLowerCase().includes(s));
    },
    databaseOptions() {
      const t = this.$t;
      return {
        none    : t('none'),
        blank   : t('useX', { x: t('blankX', { x: t('database') }) + t('ة') }),
        client  : t('useX', { x: t('XY', { 0: t('database'), 1: t('client') }) }),
        file    : `${t('import')} ${t('fromX', { attr: t('file') })}`,
      };
    },
  },
  mounted: async function () {
    this.loading = true;
    
    // sales systems
    await this.$store
      .dispatch('modules/index', { module: 'sales-systems' })
      .then(data => data.forEach(v => this.form.sales_systems.push(v)));
    
    // translations 
    await this.$store
      .dispatch('locales/getLocaleTranslations', this.getLocale() == 'ar' ? 'en' : 'ar')
      .then(() => this.translations.forEach(key => Vue.set(this.form.translations, key, { ar: this.$t(key, {}, 'ar'), en: this.$t(key, {}, 'en') })));
    
    // if update => fetch business type using its id
    if (this._action == 'update' && this._id) {
      this.$store.dispatch('business_types/find', this._id)
        .then(type => {
          this.form = this.obj_clone(type);
          setTimeout(() => {
            Vue.set(this.form, 'modules', type.modules);
            Vue.set(this.form, 'cashier_settings', type.cashier_settings);
          }, 50);
        })
        .catch(err => this.errors = err);
    }
    else this.form.app_id = this.apps[0] ? this.apps[0].id : null;
    
    this.loading = false;
  },
  methods: {
    submit: async function () {
      this.loading = true;

      let form = {};
      if (this.form.id) form.id = this.form.id;
      form.app_id = this.form.app_id;
      form.name = JSON.stringify(this.form.name);
      form.description = this.form.description;
      form.modules = JSON.stringify(this.form.modules);
      form.sales_systems = JSON.stringify(this.form.sales_systems);
      form.cashier_settings = JSON.stringify(this.form.cashier_settings);
      form.translations = JSON.stringify(this.form.translations);
      form.forms = JSON.stringify(this.form.forms);

      ['desktop', 'tablet', 'mobile'].forEach(device => form[`${device}_screenshot`] = this.screenshots[device]);

      Object.entries(this.table_imgs).forEach(img => form[img[0]] = img[1]);
      
      form.database = JSON.stringify(this.form.database);
      form.database_file = this.form.database.file || null;

      await this.$store.dispatch(`business_types/${this._action}`, form)
        .then(() => this.cancel())
        .catch(err => this.errors = err);

      this.loading = false;
    },
    cancel: function () {
      this.$router.push({ name: 'business_types.index' });
    },

    // extra forms
    addNewForm: function () {
      this.form.forms.push({
        icon: null,
        name: null,
        order: this.form.forms.length + 1,
        fields: [],
        width: '500px',
        dir: 'auto',
        active: true,
      });
    },
    removeForm: function (i) {
      this.form.forms.splice(i, 1);
    },
    addNewFormField: function (i) {  
      this.form.forms[i].fields.push({
        name: null,
        datatype: 'text',
        dir: 'auto',
        class: null,
        style: null,
        required: false,
        active: true,
        order: this.form.forms[i].fields.length + 1,
      });
    },
    removeFormField: function (i, j) {
      this.form.forms[i].fields.splice(j, 1);
      // this.form.forms[i].fields.forEach(f => {
      //   if (!this.form.forms[i].fields.find(fd => fd.order == f.order - 1))
      //     f.order--
      // });
    },
    changeFormOrder: function (i, change) {
      let forms = this.form.forms,
        current_order = forms[i].order,  
        new_order = current_order + change;

      if (new_order < 1 || new_order > forms.length)
        return;

      this.form.forms.find(f => f.order == new_order).order = current_order;
      this.form.forms[i].order = new_order;
      this.sortForms();
    },
    sortForms: function () {
      this.form.forms.sort((a, b) => a.order < b.order ? -1 : 1);
    },
    changeFieldOrder: function (i, j, change) {
      let fields = this.form.forms[i].fields,
        current_order = fields[j].order,  
        new_order = current_order + change;

      if (new_order < 1 || new_order > fields.length)
        return;

      this.form.forms[i].fields.find(f => f.order == new_order).order = current_order;
      this.form.forms[i].fields[j].order = new_order;
      this.sortFormFields(i);
    },
    sortFormFields: function (i) {
      this.form.forms[i].fields.sort((a, b) => a.order < b.order ? -1 : 1);
    },
  },
  watch: {
    "form.app_id": function (id) {
      let app = id ? this.apps[id] : null,
        modules = {},
        cashier_settings = {};

      if (app) {
        let group = null;

        // modules
        group = app.configuration_groups.find(group => group.key == 'modules');
        if (group) group.configurations.forEach(config => modules[config.key] = config.default);

        // cashier_settings
        group = app.configuration_groups.find(group => group.key == 'cashier_settings');
        if (group) group.configurations.forEach(config => cashier_settings[config.key] = config.default);
      }
        
      this.form.modules = modules;
      this.form.cashier_settings = cashier_settings;
    },
    "form.database.option": async function (option) {
      if (option == 'client') {
        this.loading_dbs = true;
        await this.$store.dispatch('clients/fetchBackups')
          .then(data => this.client_dbs = data)
          .catch(err => console.log(err));
        this.loading_dbs = false;
      } else this.form.database.file = null;
    },
    preview_device: async function (device)
    {
      if (device)
      {
        setTimeout(() => {
          const el = document.getElementById('products-grid');
          const options = { type: 'dataURL' };
          this.$html2canvas(el, options)
            .then(src => this.screenshots[device] = src)
            .catch(err => console.error(err));
        }, 200);
      }
    },
  },
  components: {
    Widget,
    MultiTab,
    Tab,
    NameInput,
    IconInput,
    BSelect,
    ProductsGrid,
  },
};
</script>