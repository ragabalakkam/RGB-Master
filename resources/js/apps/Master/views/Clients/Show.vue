<template>
  <widget
    v-model="loading"
    :title="parseName(client.name)"
    :on-created-actions="['business_types/index', 'apps/index', 'versions/index']"
  >
    <multi-tab v-model="page">

      <!-- organization -->
      <tab :name="$t('X', { 0: $t('data'), 1: $t('organization') })">

        <!-- created by -->
        <div class="text-secondary font-md mb-3">
          <b-i icon="info-circle" class="mr-1" /> {{ $t('subscribed') }} {{ $t('byX', { x: '' }) }}
          <span v-if="client.creator" class="mx-1 d-inline-block">
            <img :src="parseImg(client.creator) || '/imgs/placeholders/user.png'" class="sz-15 rounded-circle" />
            {{ client.creator.name }}
          </span>
          <span class="mr-1" v-else v-text="$t('byX', { x: $t('ال') + $t('client') })" />
          - {{ castTime(client.created_at) }}
        </div>

        <!-- image -->
        <div class="text-center mb-5">
          <b-img :src="parseImg(client)" v-open-in-viewer size="180" class="rounded-circle mx-auto mb-1" />
          <b-button v-if="client.image" class="py-1 bg-all-none border-0 text-primary" icon="trash" @click="remove_image">
            {{ $t('deleteX', { attr: $t('ال') + $t('image') }) }}
          </b-button>
        </div>
        
        <!-- client info -->
        <div class="row mb-3">
          
          <!-- name -->
          <div v-for="(locale, id) in locales" :key="`name-${id}`" class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('XInLocaleY', { x: $t('ال') + $t('name'), y: $t(locale.label)}))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.name ? client.name[locale.label] : '--'" />
            </div>
          </div>
          
          <!-- slogan -->
          <div v-for="(locale, id) in locales" :key="`slogan-${id}`" class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('XInLocaleY', { x: $t('ال') + $t('slogan'), y: $t(locale.label)}))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.slogan ? client.slogan[locale.label] : '--'" />
            </div>
          </div>
          
          <!-- tax number -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('theTaxNumber'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.tax_number || '--'" />
            </div>
          </div>
          
          <!-- commercial registration number -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('theCommercialRegNo'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.commercial_reg_no || '--'" />
            </div>
          </div>
          
          <!-- phone -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('phone'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <a v-if="client.phone" class="flex-4 d-flex flex-gap-1 align-items-center" :href="`tel:${client.phone}`">
                <b-i fas="fas" icon="phone-square" />
                <span v-text="client.phone" />
              </a>
              <p v-else class="flex-4">--</p>
            </div>
          </div>
          
          <!-- email -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('email'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <a v-if="client.email" class="flex-4 d-flex flex-gap-1 align-items-center" :href="`mailto:${client.email}`">
                <b-i fas="fas" icon="envelope-square" />
                <span v-text="client.email" />
              </a>
              <p v-else class="flex-4">--</p>
            </div>
          </div>
          
          <!-- address -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('address'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.full_address ? parseName(client.full_address) : '--'" />
              </div>
          </div>
          
          <!-- notes -->
          <div class="col-md-6">
            <div class="border-bottom pb-2 mb-2 d-flex">
              <p class="flex-1 text-dark-7" v-text="ucFirst($t('notes'))" />
              <p class="wd-30 fs-3 text-dark-7">:</p>
              <p class="flex-4" v-text="client.notes || '--'" />
              </div>
          </div>

        </div>
        
        <!-- subscribed / unsubscribed apps -->
        <div class="row" v-if="client.client_apps">
          <b-form-group class="col-md-6 mb-4" :label="$t('subscribedApps')" label-class="fs-4 text-dark">
            <div class="row px-1 mx-0 border-left border-secondary-3 border-2">
              <div v-for="(app, index) in Object.values(client.client_apps)" :key="`subscribed-${app.app_id}`" class="col-4 col-md-3 col-lg-2 px-1 mt-2">
                <b-button
                  v-if="apps[app.app_id]"
                  variant="primary"
                  class="border py-3"
                  :title="parseName(apps[app.app_id].description)"
                  @click="page = index + 1"
                  block
                >
                  <b-img :src="parseImg(apps[app.app_id])" size="60" class="rounded-lg mx-auto mb-2" />
                  <p class="fs-3" v-text="abbr(parseName(apps[app.app_id].name), wXS ? 130 : 200)" :title="parseName(apps[app.app_id].name)" />
                </b-button>
              </div>
              <p class="px-2 py-4" v-if="!client.client_apps.length" v-text="$t('thereIsNoX', { x: $t('apps') })" />
            </div>
          </b-form-group>

          <b-form-group class="col-md-6 mb-4" :label="$t('unsubscribedApps')" label-class="fs-4 text-dark">
            <div class="row px-1 mx-0 border-left border-secondary-3 border-2">
              <div v-for="app in unsubscribedApps" :key="`unsubscribed-${app.id}`" class="col-4 col-md-3 col-lg-2 px-1 mt-2">
                <a
                  v-if="app"
                  :href="`/master/apps/${app.id}`"
                  class="d-block text-center px-2 py-3 bg-light border rounded-lg"
                  :title="parseName(app.description)"
                  target="_blank"
                >
                  <b-img :src="parseImg(app)" size="65" class="rounded-lg mx-auto mb-2" />
                  <span class="d-block fs-3" v-text="abbr(parseName(app.name), wXS ? 130 : 200)" :title="parseName(app.name)" />
                </a>
              </div>

              <p class="px-2 py-4" v-if="!unsubscribedApps.length" v-text="$t('thereIsNoX', { x: $t('apps') })" />
            </div>
          </b-form-group>
        </div>

        <!-- actions -->
        <div class="d-flex flex-gap-2">

          <!-- update -->
          <b-router v-if="can('clients.update')" class="btn btn-info" :to="`/master/clients/update/${client.id}`">
            <span v-text="ucFirst($t('updateX', { attr: $t('X', { 0: $t('data'), 1: $t('organization') }) }))" />
            <b-i icon="edit" class="ml-2" />
          </b-router>

          <!-- delete -->
          <b-button v-if="can('clients.delete')" variant="danger" @click.stop="confirmDelete(client, 'clients/delete')">
            <span v-text="ucFirst($t('deleteX', { attr: $t('ال') + $t('organization') }))" />
            <b-i icon="trash" class="ml-2" />
          </b-button>

        </div>
      </tab>

      <!-- client apps (tabs) -->
      <tab v-for="app in client.client_apps" :key="app.id" :name="apps[app.app_id] ? parseName(apps[app.app_id].name) : ''">
        <div v-if="apps[app.app_id]" class="row">

          <div class="col-md-6">
            <div class="row">

              <!-- app info -->
              <section class="col-12 mb-4">
                <!-- app info -->
                <div class="card border rounded bg-light py-2 px-3 d-flex align-items-center flex-gap-3 flex-gap-md-bs">
                  <b-img :src="parseImg(apps[app.app_id])" size="45" />
                  
                  <div class="flex-1">
                    <div class="d-flex mb-1">
                      <p :class="`wd-${wXS ? '60' : '95'}`">{{ wXS ? '' : 'APP' }} ID</p>
                      <p class="wd-20 fs-3">:</p>
                      <div class="flex-1 d-flex-center flex-gap-2 text-primary" v-copy-on-click="app.id">
                        <b-button class="py-0 px-1 text-primary" icon="copy" />
                        <p class="flex-1" v-text="app.id" />
                      </div>
                    </div>
                    
                    <div class="d-flex">
                      <p :class="`wd-${wXS ? '60' : '95'}`">{{ wXS ? '' : 'APP' }} SECRET</p>
                      <p class="wd-20 fs-3">:</p>
                      <div class="flex-1 d-flex-center flex-gap-2 text-primary" v-copy-on-click="app.secret">
                        <b-button class="py-0 px-1 text-primary" icon="copy" />
                        <p class="flex-1" v-text="wXS ? '***********' : app.secret" />
                      </div>
                    </div>
                  </div>

                  <a v-if="app.installed_at" :href="app.domain" class="btn btn-light" target="_blank">
                    <span v-text="$t('XY', { 0: $t('open'), 1: wXS ? '' : $t('ال') + $t('app') })" class="mr-1 nowrap" />
                    <b-i icon="external-link" />
                  </a>
                </div>

                <!-- subscribed by -->
                <div class="mt-1 text-secondary font-md">
                  <b-i icon="info-circle" class="mr-1" /> {{ $t('subscribed') }} {{ $t('byX', { x: '' }) }}
                  <span v-if="app.creator" class="mx-1 d-inline-block">
                    <img :src="parseImg(app.creator) || '/imgs/placeholders/user.png'" class="sz-15 rounded-circle" />
                    {{ app.creator.name }}
                  </span>
                  <span class="mr-1" v-else v-text="$t('byX', { x: $t('ال') + $t('client') })" />
                  - {{ castTime(app.created_at) }}
                </div>
              </section>
              
              <!-- installed at -->
              <section class="col-md-6 mb-4">
                <div class="card border rounded bg-light p-3">
                  <form @submit.prevent="modify_installation_status(app.id, !!!app.installed_at)">
                    <b-form-group :label="$t('ال') + $t('installation')" label-class="font-lg">
                      <hr class="mt-1 mb-3" />
                      <div class="d-flex align-items-center">
                        <div class="flex-1">
                          <p class="mb-3" v-if="app.installed_at" v-text="`${$t('installed')} ${castTime(app.installed_at)}`" />
                          <p class="mb-3 text-secondary-8" v-else v-text="`${$t('notInstalledYet')}.`" />
                          <div class="d-flex flex-gap-2">
                            <a
                              v-if="isOnline(app)"
                              class="btn btn-light bg-white border py-1 px-2"
                              :href="`/storage/logs/installations/${app.id}.log`"
                              target="_blank"
                            >
                              <span class="mr-1" v-text="$t('ال') + $t('logs')" />
                              <b-i icon="file" />
                            </a>
                            <b-button
                              v-if="
                                can(`clientApps.${app.installed_at ? 'un' : ''}install`)
                                && isOnline(app)
                                && (!app.active_process || ['installation', 'uninstallation'].includes(app.active_process))
                              "
                              type="submit"
                              class="btn btn-light bg-white py-1 px-2 d-flex-center flex-gap-1"
                              :variant="app.installed_at ? 'outline-danger' : 'outline-primary'"
                              :disabled="app.active_process"
                            >
                              <span 
                                v-text="$t(`${app.installed_at ? 'uninstall' : 'install'}${app.active_process ? 'ing' : ''}`)"
                              />
                              <pulse-loader
                                v-if="app.active_process"
                                size="0.25rem"
                                :color="`var(--bs-${app.installed_at ? 'danger' : 'primary'})`"
                              />
                              <b-i v-else :icon="`${app.installed_at ? 'times' : 'check'}-circle`" />
                            </b-button>
                          </div>
                        </div>
                        <b-i fas="fad" size="4x" :icon="`${app.installed_at ? 'check' : 'times'}-circle`" :class="`text-${app.installed_at ? 'info' : 'secondary-4'}`" />
                      </div>
                    </b-form-group>
                  </form>
                </div>
              </section>
              
              <!-- licensed at -->
              <section class="col-md-6 mb-4">
                <div class="card border rounded bg-light p-3">
                  <form @submit.prevent="modify_license_status(app.id, !!!app.licensed_at)">
                    <b-form-group :label="$t('ال') + $t('license')" label-class="font-lg">
                      <hr class="mt-1 mb-3" />
                      <div class="d-flex align-items-center">
                        <div class="flex-1">
                          <p class="mb-3" v-if="app.licensed_at" v-text="`${$t('licensed')} ${castTime(app.licensed_at)}`" />
                          <p class="mb-3 text-secondary-8" v-else v-text="`${$t('notLicensedYet')}.`" />
                          <div class="d-flex flex-gap-2">
                            <b-button
                              v-if="can(`clientApps.${app.licensed_at ? 'un' : ''}license`)"
                              type="submit"
                              class="d-flex-center flex-gap-2 py-1 px-2"
                              :variant="app.licensed_at ? 'outline-danger' : 'primary'"
                              :disabled="appsDisabled[app.id]"
                              v-text="$t(app.licensed_at ? 'unlicense' : 'license')"
                            />
                          </div>
                        </div>
                        <b-i fas="fad" size="4x" icon="file-certificate" :class="`text-${app.licensed_at ? 'info' : 'secondary-4'}`" />
                      </div>
                    </b-form-group>
                  </form>
                </div>
              </section>

              <!-- version -->
              <section v-if="can('versions.index')" class="col-12 mb-4">
                <div class="card border rounded bg-light p-3">
                  <div class="d-flex flex-gap-3 flex-gap-md-4 flex-column-xs">

                    <!-- current version -->
                    <form class="flex-1" @submit.prevent="update_version(app.id, app.version_id)">
                      <b-form-group :label="$t('ال') + $t('version')" label-class="font-lg">
                        <hr class="mt-1" />
                        <b-select
                          v-if="can('clientApps.update')"
                          class="mb-2"
                          txt="number"
                          :disabled="app.installed_at"
                          :data="Object.values(versions).filter(v => v.app_id == app.app_id)"
                          v-model="app.version_id"
                        />
                        <div class="d-flex align-items-end">
                          <template v-if="can('clientApps.update')">
                            <b-button v-if="!isOnline(app)" class="font-md text-secondary-7 p-0" @click="checkForUpdates(app.id)">
                              <b-i icon="redo" class="font-sm mr-1" />
                              <span v-text="app.checked_for_updates_at ? $t('lastCheckedForUpdatesAtX', { x: castTime(app.checked_for_updates_at) }) : $t('neverCheckedForUpdates')" />
                            </b-button>
                            <b-button v-else-if="!app.installed_at" :disabled="appsDisabled[app.id]" variant="primary" type="submit" class="py-1 px-2" v-t="'update'" />
                          </template>
                          <div class="flex-1" />
                          <a class="d-block text-primary font-lg c-ptr" :href="`/master/versions/${app.version_id}`" target="_blank">
                            <small v-text="ucFirst(`${$t('ال') + $t('version')} ${app.version_number}`)" />
                            <b-i icon="external-link-square" class="ml-1 fs-3" />
                          </a>  
                        </div>
                      </b-form-group>
                    </form>

                    <!-- newer version -->
                    <form
                      v-if="app.version_id < apps[app.app_id].latest_version_id && versions[apps[app.app_id].latest_version_id]"
                      class="flex-1 bg-white rounded-lg border px-3 py-3"
                      :title="versions[apps[app.app_id].latest_version_id].description || ''"
                      @submit.prevent="update_version(app.id, apps[app.app_id].latest_version_id)"
                    >
                      <p class="text-success mb-2">
                        <b-i class="font-lg" icon="info-circle" />
                        <span class="font-lg mr-2" v-text="ucFirst($t('versionXAvailableNow', { x: versions[apps[app.app_id].latest_version_id].number }))" />
                      </p>
                      <p class="text-secondary mb-3">
                        <span v-text="`${$t('realased')} ${$t('atX', { x: castTime(versions[apps[app.app_id].latest_version_id].created_at) })}.`" />
                        <span v-text="abbr(versions[apps[app.app_id].latest_version_id].description || '', 50)" />
                      </p>
                      <b-button
                        v-if="can('clientApps.update')"
                        variant="outline-success"
                        type="submit"
                        class="py-1 d-flex-center flex-gap-1"
                        icon="layer-plus"
                        :disabled="['installation', 'uninstallation', 'update'].includes(app.active_process)"
                      >
                        <span v-text="app.active_process == 'update' ? $t('updating') : $t('updateX', { attr: $t('ال') + $t('version') })" />
                        <pulse-loader v-if="app.active_process" size="0.15rem" color="var(--bs-success)" />
                      </b-button>
                    </form>

                  </div>
                </div>
              </section>
                
              <!-- domain -->
              <section v-if="isOnline(app)" class="col-12 mb-4">
                <div class="card border rounded bg-light p-3">
                  <form @submit.prevent="update_domain(app.id, app.domain, app.root_dir)">
                    <b-form-group :label="`${$t('domain')} & ${$t('X', { 0: $t('path'), 1: $t('files') })}`" label-class="font-lg">
                      <hr class="mt-1 mb-3" />
                      
                      <b-form-group :label="$t('domain')" class="mb-3">
                        <div class="position-relative">
                          <b-input :disabled="!canModifyApps" v-model="app.domain" input-dir="ltr" />
                          <div class="position-absolute position-forced-right mx-2 d-flex flex-gap-1">
                            <b-button v-copy-on-click="app.domain" class="py-0 px-1" icon="copy" />
                            <a :href="app.domain" class="btn btn-light py-0 px-1" target="_blank"><b-i icon="external-link" /></a>
                          </div>
                        </div>
                      </b-form-group>
                      
                      <b-form-group :label="$t('X', { 0: $t('path'), 1: $t('files') })" class="mb-3">
                        <div class="position-relative">
                          <b-input :disabled="!canModifyApps" v-model="app.root_dir" input-dir="ltr" />
                          <div class="position-absolute position-forced-right mx-2">
                            <b-button v-copy-on-click="app.root_dir" class="py-0 px-1" icon="copy" />
                          </div>
                        </div>
                      </b-form-group>
                      
                      <b-button v-if="canModifyApps" :disabled="appsDisabled[app.id]" variant="primary" type="submit" class="py-1 px-2" v-t="'update'" />
                    </b-form-group>
                  </form>
                </div>
              </section>

            </div>
          </div>
          
          <div class="col-md-6">
            <div class="row">

              <!-- business type -->
              <section class="col-12 mb-4">
                <div class="card border rounded bg-light p-3" v-if="business_types[app.business_type_id]">
                  <form @submit.prevent="update_business_type(app.id, app.business_type_id, app.configurations, app.update_configurations, app.update_database)">
                    <b-form-group :label="$t('theBusinessType')" label-class="font-lg">
                      <hr class="mt-1 mb-3" />
                      <div class="d-flex flex-column-xs flex-gap-3">
                        <div class="flex-1">
                          <b-select
                            class="mb-2"
                            :data="business_types"
                            :cast-text="x => parseName(x)"
                            @change="
                              app.update_configurations_with_business_type && app.business_type_id && business_types[app.business_type_id] 
                                ? app.configurations.modules = business_types[app.business_type_id].modules
                                : null
                            "
                            :disabled="!canModifyApps"
                            v-model="app.business_type_id"
                          />
                          <div v-if="canModifyApps">
                            <b-checkbox class="mb-2 fs-3" v-model="app.update_configurations">
                              {{ $t('updateX', { attr: $t('ال') + $t('modules') }) }}
                            </b-checkbox>
                            <b-checkbox class="mb-3 fs-3" v-model="app.update_database">
                              {{ $t('updateX', { attr: $t('theDatabase') }) }}
                            </b-checkbox>
                          </div>
                          <div class="d-flex flex-gap-3 align-items-end">
                            <b-button v-if="canModifyApps" variant="primary" type="submit" class="py-1 px-2" v-t="'update'" />
                            <div class="flex-1"></div>
                            <a class="d-block text-primary font-lg c-ptr" :href="`/master/business-types/${app.business_type_id}`" target="_blank">
                              <small v-text="ucFirst(parseName(business_types[app.business_type_id].name))" />
                              <b-i icon="external-link-square" class="ml-1 fs-3" />
                            </a>  
                          </div>
                        </div>
                        <div class="d-flex flex-gap-2">
                          <div v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" class="bg-white py-1 px-2 border">
                            <p class="fs-3 mb-1">
                              <b-i :icon="device" />
                              <span v-t="device" />
                            </p>
                            <img 
                              :src="`/storage/${business_types[app.business_type_id].cashier_settings[`${device}_screenshot`]}`"
                              v-open-in-viewer
                              class="ht-60"
                            />
                          </div>
                        </div>
                      </div>
                    </b-form-group>
                  </form>
                </div>
              </section>
              
              <!-- database -->
              <section v-if="isOnline(app)" class="col-12 mb-4">
                <div class="card border rounded bg-light p-3">
                  <form @submit.prevent="update_database(app.id, app.db_driver, app.db_host, app.db_database, app.db_username, app.db_password)">
                    <b-form-group :label="$t('theDatabase')" label-class="font-lg">
                      <hr class="mt-1 mb-3" />
                      <div class="row mb-3">

                        <!-- database drive -->
                        <b-select
                          class="col-md-6 mb-4"
                          input-class="bg-white"
                          :label="$t('X', { 0: $t('type'), 1: $t('theDatabase') })"
                          :data="[{ name: 'MySQL', id: 'mysql' }, { name: 'MS SQL Server', id: 'sqlsrv' }]"
                          v-model="app.db_driver"
                          :errors="errors"
                          name="db_driver"
                          attr="driver"
                          :disabled="true"
                        />

                        <!-- database host -->
                        <b-input
                          class="col-md-6 mb-4"
                          input-class="bg-white"
                          :label="$t('host')"
                          name="db_host"
                          attr="host"
                          :errors="errors"
                          v-model="app.db_host"
                          :disabled="true"
                        />

                        <!-- database name -->
                        <b-input
                          class="col-md-6"
                          :label="$t('XY', { 0: $t('name'), 1: $t('theDatabase') })"
                          :errors="errors"
                          name="db_database"
                          attr="name"
                          label-class="fs-4"
                          v-model="app.db_database"
                          :placeholder="`example : rgbksaco_client_db`"
                          :disabled="!canModifyApps"
                        />

                        <!-- database username -->
                        <b-input
                          class="col-md-6"
                          :label="$t('username')"
                          :errors="errors"
                          name="db_username"
                          attr="username"
                          label-class="fs-4"
                          v-model="app.db_username"
                          :placeholder="`example : rgbksaco_client_user`"
                          :disabled="!canModifyApps"
                        />

                        <!-- database password -->
                        <b-password-input
                          class="col-12"
                          :label="$t('password')"
                          name="db_password"
                          attr="password"
                          :errors="errors"
                          v-model="app.db_password"
                          :disabled="!canModifyApps"
                        />

                      </div>
                      <b-button v-if="!appsDisabled[app.id]" variant="primary" type="submit" class="py-1 px-2" v-t="'update'" />
                    </b-form-group>
                  </form>

                  <hr v-if="!appsDisabled[app.id]" class="mt-4" />

                  <div v-if="canModifyApps" class="d-flex flex-column-xs">

                    <form class="flex-2 mb-3 mb-md-0" @submit.prevent="import_database(app.id, app.database_file)" enctype="multipart/form-data">
                      <b-form-group :label="`${$t('import')} ${$t('fromX', { attr: $t('file') })}`">
                        <div class="rounded-lg ht-65 bg-white position-relative mb-2">
                          <b-file-input :errors="errors" type="file" input-class="border-0 py-3" v-model="app.database_file" :disabled="!app.installed_at" />
                          <b-i icon="folder-upload" fas="fad" size="4x" class="position-absolute position-bottom-right mx-3 mb-2" :class="`text-${app.database_file ? 'info' : 'secondary-5'}`" />
                          <p class="position-absolute position-bottom mb-3 ml-3 text-secondary-8 font-sm font-bold" v-t="'attachmentMustNotExceed5mb'" />
                        </div>
                        <b-button
                          v-if="!canModifyApps"
                          variant="primary"
                          type="submit"
                          class="py-1 px-2"
                          v-t="'import'"
                          :disabled="appsDisabled[app.id] || !app.database_file"
                        />
                      </b-form-group>
                    </form>

                    <div class="border-left mx-4 mt-4 mb-3 mb-md-0" />

                    <b-form-group class="flex-1 d-flex flex-column mb-3 mb-md-0" :label="$t('theBackups')">
                      <div class="flex-1 d-flex flex-gap-2 pt-1">
                        <b-button variant="primary" class="flex-1" @click="export_database(app.id)" :disabled="!app.installed_at" >
                          <b-i icon="download" size="2x" class="mb-2" />
                          <p v-text="$t('takeBackup')" />
                        </b-button>
                        <b-button variant="primary" class="flex-1" @click="clean_database(app.id)" :disabled="!app.installed_at" >
                          <b-i icon="hand-sparkles" size="2x" class="mb-2" />
                          <p v-text="$t('wipeX', { x: $t('theDatabase') })" />
                        </b-button>
                      </div>
                    </b-form-group>

                  </div>
                </div>
              </section>

            </div>
          </div>
          
          <!-- configurations -->
          <section v-if="app.configurations" class="col-12">
            <form class="card border rounded bg-light p-3 mb-4" @submit.prevent="update_configurations(app.id, app.configurations)">
              <div class="row">
                <!-- number_of_branches -->
                <b-input
                  v-if="app.configurations.hasOwnProperty('number_of_branches')"
                  class="col-md-6"
                  :label="$t('number_of_branches')"
                  :errors="errors"
                  name="number_of_branches"
                  attr="number"
                  label-class="fs-4"
                  :disabled="!canModifyApps"
                  v-model="app.configurations.number_of_branches"
                />

                <!-- number_of_points_of_sale -->
                <b-input
                  v-if="app.configurations.hasOwnProperty('number_of_points_of_sale')"
                  class="col-md-6"
                  :label="$t('number_of_points_of_sale')"
                  :errors="errors"
                  name="number_of_points_of_sale"
                  attr="number"
                  label-class="fs-4"
                  :disabled="!canModifyApps"
                  v-model="app.configurations.number_of_points_of_sale"
                />
              </div>

              <hr />

              <b-form-group :label="$t('ال') + $t('modules')" label-class="font-lg mb-3">
                <div class="row">
                  <template v-if="appsConfigurations[app.app_id] && appsConfigurations[app.app_id].modules">
                    <div class="col-md-4" v-for="(value, name) in appsConfigurations[app.app_id].modules" :key="name">
                      <b-on-off-input
                        :disabled="!canModifyApps"
                        :class="value ? 'text-primary' : 'text-dark-7'"
                        class="pb-2 mb-2 d-flex flex-gap-2"
                        v-model="app.configurations[name]"
                      >
                        <span
                          v-if="appsConfigurations[app.app_id] && appsConfigurations[app.app_id].modules[name]"
                          v-html="parseName(appsConfigurations[app.app_id].modules[name].name)"
                        />
                      </b-on-off-input>
                    </div>
                  </template>
                </div>
                <b-button v-if="canModifyApps" variant="primary" type="submit" class="py-1 px-2" v-t="'update'"  />
              </b-form-group>
            </form>
          </section>

        </div>
        
        <!-- console window -->
        <console v-if="canModifyApps && !isOnline(app)" class="position-absolute position-bottom index-up" :app="app"/>
      </tab>

    </multi-tab>
  </widget>
</template>

<script>
import { mapGetters } from 'vuex';
import Widget from "../../../../common/masters/ControlPanel/components/Widget.vue";
import MultiTab from '../../../../common/vendor/MultiTab/MultiTab.vue';
import Tab from '../../../../common/vendor/MultiTab/Tab.vue';
import SelectBusinessType from '../../components/SelectBusinessType.vue';
import Console from './components/Console.vue';
export default {
  names: "clients.show",
  data() {
    return {
      page: 0,
      client: {},
      errors: {},
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      locales: 'locales/locales',
      business_types: 'business_types/business_types',
      versions: 'versions/versions',
      paths: 'configurations/paths',
      apps: 'apps/apps',
      all_client_apps: 'clients/client_apps',
    }),
    unsubscribedApps() {
      return this.client && this.client_apps ? this.withoutTrashed(this.apps).filter(app => !this.withoutTrashed(this.client_apps).find(a => a.app_id == app.id)) : [];
    },
    appsConfigurations() {
      let configs = {};
      Object.values(this.apps).forEach(app => {
        configs[app.id] = {};
        app.configuration_groups.forEach(gr => {
          configs[app.id][gr.key] = {};
          gr.configurations.forEach(c => {
            configs[app.id][gr.key][c.key] = c;
          });
        });
      });
      return configs;
    },
    canModifyApps() {
      return this.can('clientApps.configurations.update');
    },
    appsDisabled() {
      let disabled = {};
      this.withoutTrashed(this.client_apps).forEach(app => disabled[app.id] = !this.canModifyApps || app.active_process || !app.installed_at);
      return disabled;
    },
    client_apps() {
      return this.withoutTrashed(this.all_client_apps).filter(ca => ca.client_id == this.client.id);
    },
  },
  mounted() {
    this.$store.dispatch("configurations/show", 'paths');
    this.$store
      .dispatch("clients/find", this.$route.params.id)
      .then((client) => {
        this.client = client;
        this.loading = false;
      })
      .catch(() => this.$router.push({ name: "clients.index" }));
  },
  methods: {
    isOnline: function (client_app) {
      const app = this.apps[client_app.app_id];
      return app ? app.online : false;
    },
    exec: async function (callback) {
      this.loading = true;
      await callback();
      this.loading = false;
    },
    remove_image: async function () {
      this.exec(() => this.$store
        .dispatch('clients/remove_image', this.client.id)
        .catch(err => console.log({ err }))
      );
    },
    modify_installation_status: function (id, value) {
      this.exec(() => this.$store
        .dispatch('clients/modify_app_installation_status', { id, value })
        .catch(err => console.log({ err }))
      );
    },
    modify_license_status: function (id, value) {
      this.exec(() => this.$store
        .dispatch('clients/modify_app_license_status', { id, value })
        .catch(err => console.log({ err }))
      );
    },
    update_business_type: function (id, business_type_id, configurations, update_configurations, update_database) {
      this.exec(() => this.$store
        .dispatch('clients/update_app_business_type', { id, business_type_id, configurations, id, business_type_id, configurations, update_configurations, update_database })
        .catch(err => console.log({ err }))
      );
    },
    update_configurations: function (id, configurations) {
      this.exec(() => this.$store
        .dispatch('clients/update_app_configurations', { id, configurations })
        .catch(err => console.log({ err }))
      );
    },
    update_domain: function (id, domain, root_dir) {
      this.exec(() => this.$store
        .dispatch('clients/update_app_domain', { id, domain, root_dir })
        .catch(err => console.log({ err }))
      );
    },
    update_database: function (id, db_driver, db_host, db_database, db_username, db_password) {
      this.exec(() => this.$store
        .dispatch('clients/update_app_database', { id, db_driver, db_host, db_database, db_username, db_password })
        .catch(err => console.log({ err }))
      );
    },
    import_database: function (id, file) {
      this.exec(() => this.$store
        .dispatch('clients/import_app_database', { id, file })
        .catch(err => console.log({ err }))
      );
    },
    export_database: function (id) {
      this.exec(() => this.$store
        .dispatch('clients/export_app_database', { id })
        .then(src => this.downloadURI(`/storage/${src}`))
        .catch(err => console.log({ err }))
      );
    },
    clean_database: function (id) {
      this.exec(() => this.$store
        .dispatch('clients/clean_app_database', { id })
        .then(src => this.downloadURI(`/storage/${src}`))
        .catch(err => console.log({ err }))
      );
    },
    update_version: function (id, version_id) {
      this.exec(() => this.$store
        .dispatch('clients/update_app_version', { id, version_id })
        .catch(err => console.log({ err }))
      );
    },
    checkForUpdates: function (id) {
      this.exec(() => this.$store
        .dispatch('clients/check_app_for_update', { id })
        .catch(err => console.log({ err }))
      );
    },
  },
  components: {
    Widget,
    MultiTab,
    Tab,
    SelectBusinessType,
    Console,
  },
};
</script>