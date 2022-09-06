<template>
  <b-modal v-model="show" :outclick-close="false">
    <!-- header -->
    <template v-slot:header>
      {{ ucFirst($t('ال') + $t('reportX', { x: $t('aX', { x: $t('bug') }) })) }}
    </template>

    <!-- body -->
    <template v-slot:body>
      <div class="bg-light rounded" :class="{ 'wd-500' : !wXS }">

        <div v-if="!online" class="p-4 text-center">
          <b-i icon="info-circle" class="mb-3" size="3x" />
          <p v-text="$t('noConnection')" />
        </div>

        <div v-else-if="loading" class="py-5 text-center">
          <clip-loader size="2rem" />
          <p v-text="$t('pleaseWait')" />
        </div>

        <div v-else-if="received" class="text-center text-primary pt-5 pb-4 ">
          <b-i class="pulse mb-3" icon="envelope-open-text" size="5x" />
          <p class="fs-5 transition-0" v-text="$t('receivedReportedProblem')" />
        </div>

        <div v-else-if="errors == 'somethingWentWrong'" class="text-center text-danger p-5">
          <b-i class="mb-3" icon="times-circle" size="4x" />
          <p class="fs-5 transition-0" v-text="`${$t('somethingWentWrong')} ${$t('tryAgainLater')}`" />
        </div>

        <form v-else class="p-3" @submit="sendMessage">
          <!-- name -->
          <b-input
            :label="$t('ال') + $t('name')"
            :errors="errors"
            name="name"
            class="text-capitalize"
            v-model="form.name"
          />

          <!-- email -->
          <b-input
            :label="$t('email')"
            :errors="errors"
            type="email"
            name="email"
            v-model="form.email"
          />

          <!-- phone -->
          <b-input
            :label="$t('phone')"
            :errors="errors"
            name="phone"
            v-model="form.phone"
          />

          <!-- device -->
          <b-form-group :label="$t('X', { 0: $t('information'), 1: $t('connectToX', { x: $t('ال') + $t('device') }) })">
            <div class="d-flex flex-gap-2">
              <b-select
                :show-label="false"
                :errors="errors"
                name="device_type"
                class="flex-1 mb-0"
                :data="['anydesk', 'teamviewer'].map((id) => ({ id, name: id }))"
                :null-option-attr="$t('type')"
                v-model="form.device_type"
              />
              <b-input
                v-if="form.device_type"
                :show-label="false"
                :errors="errors"
                name="device_number"
                style="min-width: 50%"
                v-model="form.device_number"
                placeholder="xxx xxx xxx"
              />
            </div>
          </b-form-group>

          <!-- message -->
          <b-textarea
            :label="$t('ال') + $t('problem')"
            :errors="errors"
            name="message"
            v-model="form.message"
            :rows="7"
            required
          ></b-textarea>

          <!-- attachment -->
          <b-file-input
            class="my-3"
            :errors="errors"
            name="attachment"
            v-model="form.attachment"
          />
        </form>

      </div>
    </template>

    <!-- footer -->
    <template v-slot:footer>
      <b-button
        v-if="online && !loading && !received && errors != 'somethingWentWrong'"
        @click="sendMessage"
        :disabled="disabled"
        variant="primary"
        icon="paper-plane"
      >
        {{ $t("send") }}
      </b-button>
      <div class="text-center w-100 mb-2" v-else-if="received">
        <b-button variant="primary" v-t="'well'" @click="close" />
        <a
          class="d-block border-0 text-secondary-8 c-ptr text-hover-danger mt-2 font-md"
          v-text="$t('reportX', { x: $t('anotherX', { x: $t('bug') }) }) + $t('ى')"
          @click="clear"
        />
      </div>
    </template>
  </b-modal>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: "report-problem-modal",
  props: {
    online: { default: false },
  },
  data() {
    return {
      form: {
        name: null,
        email: null,
        phone: null,
        device_type: null,
        device_number: null,
        message: null,
        attachment: null,
      },
      errors: {},
      received: false,
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
      modals: "modals",
    }),
    modal() {
      return this.modals.reportProblem || {};
    },
    show: {
      set(value) {
        this.$store.dispatch("setModal", { name: "reportProblem", value });
      },
      get() {
        return this.modal.value;
      },
    },
    disabled() {
      return !this.online || !this.form.message;
    },
  },
  methods: {
    sendMessage: async function () {
      if (!this.disabled) {
        this.loading = true;
        await this.$store
          .dispatch("client/reportProblem", this.form)
          .then(() => this.received = true)
          .catch(e => this.errors = e);
        this.loading = false;
      }
    },
    clear: function () {
      let params = this.$route.params;
      this.form = {
        name: null,
        email: null,
        phone: null,
        device_type: null,
        device_number: null,
        message: null,
        attachment: null,
        client_id: params.org_id,
        client_app_id: params.id,
      };
      this.addUserInfo();
      this.errors = {};
      this.received = false;
    },
    close: function () {
      this.clear();
      this.show = false;
    },
    addUserInfo: function ()
    {
      let user = this.user;
      if (user) {
        this.form.name = user.name;
        this.form.phone = user.phone;
        this.form.email = user.email;
      }
    },
  },
  watch: {
    user: {
      handler: 'addUserInfo',
      deep: true,
      immediate: true,
    },
    show: 'clear',
  },
};
</script>