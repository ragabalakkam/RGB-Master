<template>
  <multiple-step-form
    v-model="show"
    class="register-form"
    :class="$route.name === 'register' ? 'bg-none' : ''"
    :formClass="`position-relative ${$route.name === 'register' && wXS ? 'w-100 h-100' : 'rounded-xl overflow-hidden'}`"
    :outClickIsCancel="outClickIsCancel"
    :errors="errors"
    :isLoading="loading"
    @proceed="register"
    @errors="(err) => (errors = err)"
    @validated-0="sendPhoneVerificationSms"
    :proceedDisabled="!form.acceptTerms"
  >
    <template v-slot:header>
      <b-img :src="`/storage/${app.logo}`" size="75" alt="logo" class="container-fluid w-50 mb-2" />
      <p class="h1 text-primary mb-2" v-if="app" v-text="parseName(app.name)" />
      <p class="text-capitalize" v-t="$route.name.replace('-', ' ')" />

      <!-- languages menu -->
      <div class="dropdown d-flex-center position-absolute position-top">
        <b-button
          class="d-flex-center bg-all-none flex-gap-2 rounded-rop-0 border-0"
          id="languages-dropdown-menu"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <b-i icon="angle-down" class="fs-3 text-secondary-5" />
          <img
            :src="`/imgs/flags/${getLocale()}.jpg`"
            class="wd-25 rounded-flag"
          />
        </b-button>
        <languages-menu aria-labelledby="languages-dropdown-menu" />
      </div>
    </template>

    <!-- Slide 01 | name & email & phone -->
    <multiple-step-form-slide
      class="text-left"
      :valid="form.name && form.email && form.phone"
      :validate="[
        { name: 'name', value: form.name, handler: 'required' },
        { name: 'email', value: form.email, handler: 'unique' },
        { name: 'phone', value: form.phone, handler: 'unique' },
      ]"
    >
      <!-- Name -->
      <b-input name="name" :errors="errors" :label="$t('ال') + $t('name')" v-model="form.name" />

      <!-- Email -->
      <b-input type="email" name="email" :errors="errors" :label="$t('email')" v-model="form.email" />

      <!-- Phone -->
      <phone-input input-class="no-spinners" :errors="errors" v-model="form.phone" />
    </multiple-step-form-slide>

    <!-- Slide 02 | verify phone number -->
    <multiple-step-form-slide
      v-if="hasSmsModule"
      class="pt-4"
      :valid="verificationSkipped || (form.phoneVerificationCode && form.phoneVerificationCode.length)"
      :validate="phoneVerificationValidation"
    >
      <template v-if="!verificationSkipped">
        <p class="mb-4" v-t="'phoneVerification.message'" />

        <b-input
          v-model="form.phoneVerificationCode"
          :errors="errors"
          name="phoneVerificationCode"
          :show-label="false"
        />

        <div class="text-center">
          <button
            class="btn btn-light text-primary bg-all-none no-shadow border-0 mb-4"
            type="button"
            @click="verificationSkipped = true"
            v-t="'skip'"
          />

          <p v-t="`phoneVerification.didntReceive?`" />

          <b-button
            class="btn-block bg-all-none border-0 text-info text-hover-primary d-flex-center"
            @click="sendPhoneVerificationSms"
          >
            <span v-t="'phoneVerification.resend'" class="mx-2" />
            <b-i icon="history" />
          </b-button>
        </div>
      </template>
      <div class="font-xl transition-0 font-info" v-else>
        <b-i icon="mobile" size="3x mx-auto d-block mb-3" />
        <span v-t="'phoneVerificationSkipped'" class="font-DroidKufi fs-4" />
      </div>
    </multiple-step-form-slide>

    <!-- Slide 03 | password & confirm password -->
    <multiple-step-form-slide :valid="validPasswords">
      <set-password-form
        :errors="errors"
        :passwords="{
          password: form.password,
          confirmPassword: form.confirmPassword,
        }"
        v-model="validPasswords"
        @update-password="(val) => (form.password = val)"
        @update-confirm-password="(val) => (form.confirmPassword = val)"
        :name="form.name"
      />
    </multiple-step-form-slide>

    <!-- Slide 04 | Accept terms and conditions -->
    <multiple-step-form-slide class="pb-3" :valid="form.acceptTerms">
      <div class="border-left border-1 pl-2 ht-200 my-3 overflow-scroll fs-3 text-left" style="white-space: pre-wrap;" v-text="termsAndConditions" /> 
      <div class="form-group position-relative mx-auto font-DroidKufi" :class="`text-${form.acceptTerms ? 'primary' : 'secondary-5'}`">
        <input type="checkbox" id="acceptTerms" class="d-none" v-model="form.acceptTerms" />
        <label for="acceptTerms" class="c-ptr no-select d-flex-center">
          <b-i :icon="`${form.acceptTerms ? 'check-' : ''}circle`" />
          <span class="ml-1" v-t="'acceptTerms'" />
        </label>
      </div>
    </multiple-step-form-slide>

    <!-- proceed button -->
    <template v-slot:proceedBtn>{{ $t("register") }}</template>

    <!-- Footer -->
    <template v-slot:footer>
      <div class="d-flex">
        <p class="flex-1 text-left" v-t="'alreadyHaveAccount'" />
        <router-link class="text-info" :to="{ name: 'login' }" v-t="'login'" />
      </div>

      <!-- license -->
      <license style="margin: 1rem -3rem -3rem -3rem;" />
    </template>
  </multiple-step-form>
</template>

<script>
import { mapGetters } from "vuex";
const AuthForm = () => import("../../../common/masters/Auth/AuthForm");
const MultipleStepForm = () => import("../../../common/vendor/MultipleStepForm/MultipleStepForm");
const MultipleStepFormSlide = () => import("../../../common/vendor/MultipleStepForm/MultipleStepFormSlide");
const SetPasswordForm = () => import("../../../common/components/SetPasswordForm");
const LanguagesMenu = () => import("../../../common/masters/ControlPanel/Menus/LanguagesMenu");
const License = () => import('./License.vue');
const PhoneInput = () => import('../../../common/components/Inputs/PhoneInput.vue');
export default {
  name: "register-form",
  props: {
    outClickIsCancel: { default: false },
  },
  data() {
    return {
      show: true,
      loading: false,
      form: {
        name: null,
        email: null,
        phone: null,
        password: '',
        confirmPassword: '',
        phoneVerificationCode: null,
        acceptTerms: true,
      },
      codeSent: false,
      errors: {},
      validPasswords: false,
      verificationSkipped: false,
    };
  },
  computed: {
    ...mapGetters({
      app: "configurations/app",
    }),
    phoneVerificationValidation() {
      return this.verificationSkipped
        ? []
        : [
            {
              name: "phoneVerificationCode",
              value: this.form.phoneVerificationCode,
              handler: this.verifyPhone,
            },
          ];
    },
    termsAndConditions() {
      return {
        en: `
Please read these Terms and Conditions carefully before using the website and the service operated by Afaq Al-Khaleej Organization for Information Technologies.

Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.

By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service.


Subscriptions

Some parts of the Service are billed on a subscription basis ("Subscription(s)"). You will be billed in advance on a recurring ...
The Subscriptions section is for SaaS businesses. For the full disclosure section, create your own Terms and Conditions.


Content

Our Service allows you to post, link, store, share and otherwise make available certain information, text, graphics, videos, or other material ("Content"). You are responsible for the …
The Content section is for businesses that allow users to create, edit, share, make content on their websites or apps. For the full disclosure section, create your own Terms and Conditions.


Links To Other Web Sites

Our Service may contain links to third-party web sites or services that are not owned or controlled by My Company (change this).
My Company (change this) has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that My Company (change this) shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.


Changes

We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 (change this) days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.


Contact Us

If you have any questions about these Terms, please contact us.
        `,
        ar:  `
يرجى قراءة هذه الشروط والأحكام بعناية قبل استخدام الموقع والخدمة التي تديرها مؤسسة آفاق الخليج لتقنية المعلومات.

إن وصولك إلى الخدمة واستخدامها مشروط بقبولك لهذه الشروط والامتثال لها. تنطبق هذه الشروط على جميع الزوار والمستخدمين وغيرهم ممن يصلون إلى الخدمة أو يستخدمونها.

من خلال الوصول إلى الخدمة أو استخدامها ، فإنك توافق على الالتزام بهذه الشروط. إذا كنت لا توافق على أي جزء من الشروط ، فلا يجوز لك الوصول إلى الخدمة.


الاشتراكات

تتم محاسبة بعض أجزاء الخدمة على أساس الاشتراك ("الاشتراكات"). ستتم محاسبتك مقدمًا على ...
قسم الاشتراكات مخصص للشركات SaaS. بالنسبة لقسم الإفصاح الكامل ، قم بإنشاء الشروط والأحكام الخاصة بك.


المحتوى

تتيح لك خدمتنا نشر معلومات أو نصوص أو رسومات أو مقاطع فيديو أو مواد أخرى أو ربطها أو تخزينها أو مشاركتها أو إتاحتها بطريقة أخرى ("المحتوى"). أنت مسؤول عن ...
قسم المحتوى مخصص للشركات التي تسمح للمستخدمين بإنشاء محتوى أو تعديله أو مشاركته أو إنشاءه على مواقع الويب أو التطبيقات الخاصة بهم. بالنسبة لقسم الإفصاح الكامل ، قم بإنشاء الشروط والأحكام الخاصة بك.


روابط لمواقع ويب أخرى

قد تحتوي خدمتنا على روابط لمواقع أو خدمات ويب تابعة لجهات خارجية ليست مملوكة أو خاضعة لسيطرة شركتي (قم بتغيير هذا).
لا تتحكم شركتي (تغيير هذا) في المحتوى أو سياسات الخصوصية أو الممارسات الخاصة بأي مواقع ويب أو خدمات تابعة لجهات خارجية ولا تتحمل أي مسؤولية عنها. أنت تقر وتوافق أيضًا على أن شركتي (تغيير هذا) لن تكون مسؤولة أو مسؤولة ، بشكل مباشر أو غير مباشر ، عن أي ضرر أو خسارة ناتجة أو يُزعم أنها ناجمة عن أو فيما يتعلق باستخدام أو الاعتماد على أي محتوى أو سلع أو الخدمات المتاحة على أو من خلال أي من مواقع الويب أو الخدمات.


التغييرات

نحتفظ بالحق ، وفقًا لتقديرنا الخاص ، في تعديل أو استبدال هذه الشروط في أي وقت. إذا كانت المراجعة جوهرية ، فسنحاول تقديم إشعار قبل 30 يومًا على الأقل (تغيير هذا) قبل دخول أي شروط جديدة حيز التنفيذ. سيتم تحديد ما يشكل تغييرًا جوهريًا وفقًا لتقديرنا الخاص.


اتصل بنا

إذا كانت لديك أي أسئلة حول هذه الشروط ، فيرجى الاتصال بنا.
        `,
      }[this.getLocale()];
    },
    hasSmsModule() {
      return this.hasModule('smsServiceUser');
    },
  },
  methods: {
    register: async function () {
      this.loading = true;
      if (this.form.acceptTerms) {
        await this.$store
          .dispatch("auth/register", this.form)
          .then(() => this.$emit("proceeded"))
          .catch((errors) => (this.errors = errors));
      }
    },
    sendPhoneVerificationSms: async function () {
      this.loading = true;
      if (this.hasSmsModule && !this.codeSent) {
        await axios.post("/api/v1/send-phone-verification-sms", { phone: this.form.phone })
        .then(({ data }) => {
          this.codeSent = true;
          if (this.$store.getters['app'].demo && data)
            this.form.phoneVerificationCode = data;
        })
      }
      this.loading = false;
    },
    verifyPhone: async function () {
      if (this.hasSmsModule) {
        return new Promise((resolve, reject) => {
          axios
            .post("/api/v1/verify-phone", {
              token: this.form.phoneVerificationCode,
              phone: this.form.phone,
            })
            .then(() => resolve())
            .catch(({ response }) => {
              let errors = response.data.errors;
              reject({ ...errors.phone, ...errors.token });
            });
        });
      }
    },
  },
  mounted() {
    if (this.$store.getters['app'].demo) {
      this.form = {
        name: "RGB Support 2",
        email: "rgbsupport2@gmail.com",
        phone: "96655" + Math.ceil(Math.random() * 10000000),
        password: "passw&rd",
        confirmPassword: "passw&rd",
        phoneVerificationCode: null,
        acceptTerms: true,
      };
    }
  },
  components: {
    AuthForm,
    MultipleStepForm,
    MultipleStepFormSlide,
    SetPasswordForm,
    LanguagesMenu,
    License,
    PhoneInput,
  },
};
</script>

<style lang="scss">
</style>