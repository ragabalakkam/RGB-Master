<template>
  <div>
    <!--  -->
    <widget class="manage-email-notifications" v-if="!loading">
      <!-- username & password -->
      <form @submit.prevent="submit" @reset.prevent="reset">
        <!-- Username & Password -->
        <b-group class="my-4" :title="$t('credentials')" titleClass="text-secondary">
          <div class="row">
            <!-- username -->
            <b-input
              class="col-md-6"
              name="email_username"
              :errors="errors"
              v-model="form.email_username"
              :label="ucFirst($t('username'))"
              :disabled="!editingCredentials"
            />

            <!-- password -->
            <b-input
              class="col-md-6"
              name="email_password"
              type="password"
              :errors="errors"
              v-model="form.email_password"
              :label="ucFirst($t('password'))"
              :disabled="!editingCredentials"
            />
          </div>
        </b-group>

        <div class="row">
          <!-- port -->
          <b-input class="col-md-6" :errors="errors" name="email_port" v-model="form.email_port" :label="ucFirst($t('port'))" :disabled="!editingCredentials" />

          <!-- host -->
          <b-input class="col-md-6" :errors="errors" name="email_host" v-model="form.email_host" :label="ucFirst($t('host'))" :disabled="!editingCredentials" />

          <!-- from name -->
          <b-input class="col-md-6" :errors="errors" name="email_from_name" v-model="form.email_from_name" :label="ucFirst($t('from_name'))" :disabled="!editingCredentials" />

          <!-- from address -->
          <b-input class="col-md-6" :errors="errors" name="email_from_address" v-model="form.email_from_address" :label="ucFirst($t('from_address'))" :disabled="!editingCredentials" />

          <!-- encryption -->
          <b-select
            class="col-12"
            :errors="errors"
            name="email_encryption"
            val="name"
            v-model="form.email_encryption"
            :data="encryptionOptions"
            :label="ucFirst($t('encryption'))"
            :disabled="!editingCredentials"
          />
        </div>

        <!-- buttons -->
        <div class="mt-3">
          <div class="d-flex flex-gap-2" v-if="editingCredentials">
            <b-button variant="primary" v-t="'update'" type="submit" />
            <b-button v-t="'cancel'" type="reset" />
          </div>

          <button v-else class="border-0 bg-none text-primary" type="button" @click="editingCredentials = true" v-text="$t('editX', { attr: $t('credentials') })" />
        </div>
      </form>
    </widget>

    <!-- loading -->
    <widget v-show="loading" class="py-5 d-flex-center">
      <clip-loader />
    </widget>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../masters/ControlPanel/components/Widget");
export default {
  name: "manage-email-notifications",
  data() {
    return {
      form: {
        email_host: null,
        email_from_name: null,
        email_from_address: null,
        email_username: null,
        email_password: null,
        email_encryption: null,
      },
      encryptionOptions: [{ name: "tls" }],
      errors: {},
      loading: false,
      editingCredentials: false,
    };
  },
  computed: {
    ...mapGetters({
      email_credentials: "services/email_credentials",
    }),
  },
  mounted: async function () {
    this.loading = true;
    await this.$store
      .dispatch("services/show", { service: 'email'})
      .then(() => this.reset());
    this.loading = false;
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store
        .dispatch("services/update", { form: this.form, service: 'email' })
        .then(() => this.reset());
      this.loading = false;
    },
    reset: function () {
      this.errors = {};
      this.form = this.obj_clone(this.email_credentials);
      this.editingCredentials = false;
    },
  },
  components: {
    Widget,
  },
};
</script>