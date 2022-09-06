<template>
  <div v-if="!loading">
    <!-- Quickstart -->
    <a 
      v-if="!sms_credentials && !sms_info"
      class="btn btn-success text-white btn-block my-4 px-3 py-4 shadow"
      href="https://sms.malath.net.sa"
      :class="{ 'd-flex align-items-center': !wXS }"
      target="_blank"
    >
      <span
        class="d-block fs-8 flex-1 text-left mb-3 mb-sm-0"
        v-t="'quickstart'"
      />
      <div class="border border-white text-white rounded-lg p-2 d-flex-center">
        <span class="mr-2"> https://sms.malath.net.sa </span>
        <b-i icon="external-link-square-alt" />
      </div>
    </a>

    <!--  -->
    <widget class="manage-sms-notifications" >
      <!-- username & password -->
      <form @submit.prevent="submit" @reset.prevent="reset">
        <b-group class="my-4" :title="$t('credentials')" titleClass="text-secondary">
          <div class="row">
            <!-- username -->
            <b-input
              class="col-md-6"
              name="sms_username"
              :errors="errors"
              v-model="form.sms_username"
              :label="ucFirst($t('username'))"
              :disabled="!editingCredentials"
            />

            <!-- password -->
            <b-input
              class="col-md-6"
              name="sms_password"
              type="password"
              :errors="errors"
              v-model="form.sms_password"
              :label="ucFirst($t('password'))"
              :disabled="!editingCredentials"
            />
          </div>
        </b-group>

        <!-- text encode-->
        <b-select
          :errors="errors"
          name="text_encode"
          v-model="form.sms_text_encode"
          :label="ucFirst($t('textEncode'))"
          :disabled="!editingCredentials"
          :data="textEncodeOptions"
          txt="text"
          val="value"
        />

        <!-- buttons -->
        <div class="mt-3">
          <div class="d-flex flex-gap-2" v-if="editingCredentials">
            <b-button variant="primary" v-t="'update'" type="submit" />
            <b-button v-t="'cancel'" type="reset" />
          </div>

          <button
            v-else
            class="border-0 bg-none text-primary"
            type="button"
            @click="editingCredentials = true"
            v-text=" $t('editX', { attr: `${$t('credentials')} ${$t('and')} ${$t('textEncode')}` })"
          />
        </div>
      </form>
    </widget>

    <!-- info -->
    <widget v-if="!loading && sms_info">
      <div class="row mx-0 text-center">
        <div
          class="col-md-6"
          :class="wXS ? 'border-bottom pb-3 mb-3' : 'border-right'"
        >
          <p class="mb-2">Remaining points</p>
          <p v-text="sms_info.credits" class="fs-8" />
        </div>

        <div class="col-md-6">
          <p class="mb-2">Senders</p>
          <p
            v-for="sender in sms_info.senders"
            :key="sender"
            v-text="sender"
            class="fs-8"
          />
        </div>
      </div>
    </widget>
  </div>

  <!-- loading -->
  <widget v-else class="py-5 d-flex-center">
    <clip-loader />
  </widget>
</template>

<script>
import { mapGetters } from "vuex";
const Widget = () => import("../../../masters/ControlPanel/components/Widget");
export default {
  name: "manage-sms-notifications",
  data() {
    return {
      form: {
        sms_username: null,
        sms_password: null,
        sms_text_encode: "utf-8 (recommended)",
      },
      errors: {},
      loading: false,
      editingCredentials: false,
      textEncodeOptions: [
        { text: "utf-8 (recommended)", value: "UTF-8" },
        { text: "ASMO-708", value: "ASMO-708" },
        { text: "big5", value: "big5" },
        { text: "cp1025", value: "cp1025" },
        { text: "cp866", value: "cp866" },
        { text: "cp875", value: "cp875" },
        { text: "csISO2022JP", value: "csISO2022JP" },
        { text: "DOS-720", value: "DOS-720" },
        { text: "DOS-862", value: "DOS-862" },
        { text: "EUC-CN", value: "EUC-CN" },
        { text: "EUC-JP", value: "EUC-JP" },
        { text: "euc-jp", value: "euc-jp" },
        { text: "euc-kr", value: "euc-kr" },
        { text: "GB18030", value: "GB18030" },
        { text: "gb2312", value: "gb2312" },
        { text: "hz-gb-2312", value: "hz-gb-2312" },
        { text: "IBM00858", value: "IBM00858" },
        { text: "IBM00924", value: "IBM00924" },
        { text: "IBM01047", value: "IBM01047" },
        { text: "IBM01140", value: "IBM01140" },
        { text: "IBM01141", value: "IBM01141" },
        { text: "IBM01142", value: "IBM01142" },
        { text: "IBM01143", value: "IBM01143" },
        { text: "IBM01144", value: "IBM01144" },
        { text: "IBM01145", value: "IBM01145" },
        { text: "IBM01146", value: "IBM01146" },
        { text: "IBM01147", value: "IBM01147" },
        { text: "IBM01148", value: "IBM01148" },
        { text: "IBM01149", value: "IBM01149" },
        { text: "IBM037", value: "IBM037" },
        { text: "IBM1026", value: "IBM1026" },
        { text: "IBM273", value: "IBM273" },
        { text: "IBM277", value: "IBM277" },
        { text: "IBM278", value: "IBM278" },
        { text: "IBM280", value: "IBM280" },
        { text: "IBM284", value: "IBM284" },
        { text: "IBM285", value: "IBM285" },
        { text: "IBM290", value: "IBM290" },
        { text: "IBM297", value: "IBM297" },
        { text: "IBM420", value: "IBM420" },
        { text: "IBM423", value: "IBM423" },
        { text: "IBM424", value: "IBM424" },
        { text: "IBM437", value: "IBM437" },
        { text: "IBM500", value: "IBM500" },
        { text: "ibm737", value: "ibm737" },
        { text: "ibm775", value: "ibm775" },
        { text: "ibm850", value: "ibm850" },
        { text: "ibm852", value: "ibm852" },
        { text: "IBM855", value: "IBM855" },
        { text: "ibm857", value: "ibm857" },
        { text: "IBM860", value: "IBM860" },
        { text: "ibm861", value: "ibm861" },
        { text: "IBM863", value: "IBM863" },
        { text: "IBM864", value: "IBM864" },
        { text: "IBM865", value: "IBM865" },
        { text: "ibm869", value: "ibm869" },
        { text: "IBM870", value: "IBM870" },
        { text: "IBM871", value: "IBM871" },
        { text: "IBM880", value: "IBM880" },
        { text: "IBM905", value: "IBM905" },
        { text: "IBM-Thai", value: "IBM-Thai" },
        { text: "iso-2022-jp", value: "iso-2022-jp" },
        { text: "iso-2022-kr", value: "iso-2022-kr" },
        { text: "iso-8859-1", value: "iso-8859-1" },
        { text: "iso-8859-13", value: "iso-8859-13" },
        { text: "iso-8859-15", value: "iso-8859-15" },
        { text: "iso-8859-2", value: "iso-8859-2" },
        { text: "iso-8859-3", value: "iso-8859-3" },
        { text: "iso-8859-4", value: "iso-8859-4" },
        { text: "iso-8859-5", value: "iso-8859-5" },
        { text: "iso-8859-6", value: "iso-8859-6" },
        { text: "iso-8859-7", value: "iso-8859-7" },
        { text: "iso-8859-8", value: "iso-8859-8" },
        { text: "iso-8859-8-i", value: "iso-8859-8-i" },
        { text: "iso-8859-9", value: "iso-8859-9" },
        { text: "Johab", value: "Johab" },
        { text: "koi8-r", value: "koi8-r" },
        { text: "koi8-u", value: "koi8-u" },
        { text: "ks_c_5601-1987", value: "ks_c_5601-1987" },
        { text: "macintosh", value: "macintosh" },
        { text: "shift_jis", value: "shift_jis" },
        { text: "us-ascii", value: "us-ascii" },
        { text: "utf-16", value: "utf-16" },
        { text: "utf-16BE", value: "utf-16BE" },
        { text: "utf-32", value: "utf-32" },
        { text: "utf-32BE", value: "utf-32BE" },
        { text: "utf-7", value: "utf-7" },
        { text: "utf-8", value: "utf-8" },
        { text: "windows-1250", value: "windows-1250" },
        { text: "windows-1251", value: "windows-1251" },
        { text: "Windows-1252", value: "Windows-1252" },
        { text: "windows-1253", value: "windows-1253" },
        { text: "windows-1254", value: "windows-1254" },
        { text: "windows-1255", value: "windows-1255" },
        { text: "windows-1256", value: "windows-1256" },
        { text: "windows-1257", value: "windows-1257" },
        { text: "windows-1258", value: "windows-1258" },
        { text: "windows-874", value: "windows-874" },
        { text: "x-Chinese-CNS", value: "x-Chinese-CNS" },
        { text: "x-Chinese-Eten", value: "x-Chinese-Eten" },
        { text: "x-cp20001", value: "x-cp20001" },
        { text: "x-cp20003", value: "x-cp20003" },
        { text: "x-cp20004", value: "x-cp20004" },
        { text: "x-cp20005", value: "x-cp20005" },
        { text: "x-cp20261", value: "x-cp20261" },
        { text: "x-cp20269", value: "x-cp20269" },
        { text: "x-cp20936", value: "x-cp20936" },
        { text: "x-cp20949", value: "x-cp20949" },
        { text: "x-cp50227", value: "x-cp50227" },
        { text: "x-EBCDIC-KoreanExtended", value: "x-EBCDIC-KoreanExtended" },
        { text: "x-Europa", value: "x-Europa" },
        { text: "x-IA5", value: "x-IA5" },
        { text: "x-IA5-German", value: "x-IA5-German" },
        { text: "x-IA5-Norwegian", value: "x-IA5-Norwegian" },
        { text: "x-IA5-Swedish", value: "x-IA5-Swedish" },
        { text: "x-iscii-as", value: "x-iscii-as" },
        { text: "x-iscii-be", value: "x-iscii-be" },
        { text: "x-iscii-de", value: "x-iscii-de" },
        { text: "x-iscii-gu", value: "x-iscii-gu" },
        { text: "x-iscii-ka", value: "x-iscii-ka" },
        { text: "x-iscii-ma", value: "x-iscii-ma" },
        { text: "x-iscii-or", value: "x-iscii-or" },
        { text: "x-iscii-pa", value: "x-iscii-pa" },
        { text: "x-iscii-ta", value: "x-iscii-ta" },
        { text: "x-iscii-te", value: "x-iscii-te" },
        { text: "x-mac-arabic", value: "x-mac-arabic" },
        { text: "x-mac-ce", value: "x-mac-ce" },
        { text: "x-mac-chinesesimp", value: "x-mac-chinesesimp" },
        { text: "x-mac-chinesetrad", value: "x-mac-chinesetrad" },
        { text: "x-mac-croatian", value: "x-mac-croatian" },
        { text: "x-mac-cyrillic", value: "x-mac-cyrillic" },
        { text: "x-mac-greek", value: "x-mac-greek" },
        { text: "x-mac-hebrew", value: "x-mac-hebrew" },
        { text: "x-mac-icelandic", value: "x-mac-icelandic" },
        { text: "x-mac-japanese", value: "x-mac-japanese" },
        { text: "x-mac-korean", value: "x-mac-korean" },
        { text: "x-mac-romanian", value: "x-mac-romanian" },
        { text: "x-mac-thai", value: "x-mac-thai" },
        { text: "x-mac-turkish", value: "x-mac-turkish" },
        { text: "x-mac-ukrainian", value: "x-mac-ukrainian" },
      ],
    };
  },
  computed: {
    ...mapGetters({
      sms_credentials: "services/sms_credentials",
      sms_info: "services/sms_info",
    }),
  },
  mounted: async function () {
    this.loading = true;
    await this.$store
      .dispatch("services/show", { service: "sms" })
      .then(() => this.reset());
    this.loading = false;
  },
  methods: {
    submit: async function () {
      this.loading = true;
      await this.$store
        .dispatch("services/update", { form: this.form, service: "sms" })
        .then(() => this.reset())
        .catch((errors) => (this.errors = errors));
      this.loading = false;
    },
    reset: function () {
      this.errors = {};
      this.form = this.obj_clone(this.sms_credentials);
      this.editingCredentials = false;
    },
  },
  components: {
    Widget,
  },
};
</script>

<style>
</style>