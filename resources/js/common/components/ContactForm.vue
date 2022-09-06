<template>
  <div>
    <div v-if="loading" class="py-5 text-center">
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

    <template v-else>
      <p class="h3 mb-4 mb-md-5" v-text="$t('contactUsForMoreInformation')" />
      <form @submit.prevent="sendContactMail">
        <!-- name -->
        <b-input
          :label="$t('ال') + $t('name')"
          :errors="errors"
          name="name"
          class="text-capitalize"
          v-model="form.name"
          required
        />

        <!-- email -->
        <b-input
          :label="$t('email')"
          :errors="errors"
          type="email"
          name="email"
          v-model="form.email"
          required
        />

        <!-- phone -->
        <b-input
          :label="$t('phone')"
          :errors="errors"
          name="phone"
          v-model="form.phone"
          required
        />

        <!-- message -->
        <b-textarea
          class="mb-4"
          :label="$t('ال') + $t('message')"
          :errors="errors"
          name="message"
          v-model="form.message"
          :rows="7"
          required
        ></b-textarea>

        <!-- submit -->
        <b-button
          v-if="!loading && !received && errors != 'somethingWentWrong'"
          type="submit"
          :disabled="disabled"
          variant="primary"
          icon="paper-plane"
        >
          {{ $t("send") }}
        </b-button>
      </form>
    </template>
  </div>
</template>

<script>
export default {
  name: "contact-form",
  data() {
    return {
      loading: false,
      form: {
        name: null,
        email: null,
        phone: null,
        message: null,
      },
      received: false,
      errors: {},
      loading: false,
    };
  },
  computed: {
    disabled() {
      return this.loading || Object.values(this.form).indexOf((x) => !x) !== -1;
    },
  },
  mounted() {
    let user = this.user;
    if (user) {
      this.form.name = user.name;
      this.form.phone = user.phone;
      this.form.email = user.email;
    }
  },
  methods: {
    sendContactMail: async function () {
      if (!this.disabled) {
        this.loading = true;
        await this.$store
          .dispatch("client/contact", this.form)
          .then(() => (this.received = true))
          .catch((e) => (this.errors = e));
        this.loading = false;
      }
    },
  },
};
</script>