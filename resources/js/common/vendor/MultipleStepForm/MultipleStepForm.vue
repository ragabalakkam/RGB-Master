<template>
  <div
    v-if="show"
    class="multiple-step-form-container position-absolute position-top-left vw-100 vh-100 d-flex-center"
    @click="e => { if (outClickIsCancel && e.target.classList.contains('multiple-step-form-container')) cancel(); }"
  >
    <div
      :class="`${formClass} ${wXS ? 'w-100' : 'wd-350'}`"
      class="multiple-step-form d-flex flex-column bg-white"
    >
      <!-- loading -->
      <div class="h-100 p-5 d-flex-center" v-if="loading">
        <clip-loader />
      </div>

      <!-- form -->
      <form
        class="py-5 h-100 d-flex flex-column"
        v-show="!loading"
        @submit.prevent="valid ? currentSlide !== slides.length - 1 ? moveToSlide(currentSlide + 1) : proceed() : null"
      >
        <!-- Custom Header -->
        <div class="px-5" v-if="$slots['header']">
          <slot name="header" />
        </div>

        <!-- Header -->
        <header class="px-5 d-flex-center" v-if="showHeaderIcons">
          <b-button
            v-for="(slide, index) in slides"
            :key="index"
            class="header-icon rounded-circle d-flex-center"
            :variant="slide.passed ? 'primary' : currentSlide === index ? 'outline-primary' : 'light'"
            :class="[{ 'bg-primary text-light': slide.passed }, { passed: slide.passed }]"
            :style="slide.passed && index === currentSlide ? 'background-color: #2176bd !important; border-color: #2176bd !important;' : null"
            :disabled="!slide.passed"
            @click="moveToSlide(index)"
          >
            <b-i :icon="slide.icon" />
          </b-button>
        </header>

        <!-- Main : Form -->
        <main class="flex-1 w-100 overflow-hidden h-100">
          <div class="dynamic d-flex align-items-center h-100" :style="cssVars">
            <slot />
          </div>
        </main>

        <!-- Footer -->
        <footer class="d-flex-center px-5">
          <!-- button | previous -->
          <b-button
            v-if="currentSlide > 0"
            variant="light"
            type="button"
            class="mx-1 font-md bg-none border-0 text-secondary text-hover-black p-0"
            style="height: 1.5rem; width: 3.5rem"
            @click="moveToSlide(currentSlide - 1)"
          >
            <slot v-if="$slots['previousBtn']" name="previousBtn" />
            <div v-else>
              <b-i icon="angle-left" class="mr-1" />
              <span v-t="'previous'" />
            </div>
          </b-button>
          <!-- button | proceed -->
          <b-button
            v-if="currentSlide === slides.length - 1"
            :variant="proceedVariant"
            type="submit"
            class="mx-1"
            :disabled="proceedDisabled"
          >
            <slot v-if="$slots['proceedBtn']" name="proceedBtn" />
            <div v-else v-html="$t('proceed')" />
          </b-button>
          <!-- button | next -->
          <b-button
            :disabled="!valid"
            variant="primary"
            type="submit"
            class="mx-1"
            tabindex="10"
            v-else
          >
            <slot v-if="$slots['nextBtn']" name="nextBtn" />
            <div v-else>
              <!-- <span v-text="$t('next')" /> -->
              <span v-text="slides[currentSlide] ? slides[currentSlide].nextBtn || $t('next') : null" />
              <b-i icon="angle-right" class="ml-1" />
            </div>
          </b-button>
        </footer>

        <!-- Custom Footer -->
        <div class="px-5" v-if="$slots['footer']">
          <hr class="mt-4 mb-3" />
          <slot name="footer" />
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "multiple-step-form",
  props: {
    value             : { required: true },
    errors            : { required: false },
    isLoading         : { required: false },
    proceedVariant    : { default: "success" },
    proceedDisabled   : { default: false },
    showHeaderIcons   : { type: Boolean, required: false, default: false },
    outClickIsCancel  : { type: Boolean, required: false, default: true },
    formClass         : { type: String, required: false },
  },
  data() {
    return {
      currentSlide: 0,
      slides: [],
      formErrors: {},
      mustBeUnique: {},
      style: { margin: 0, width: 0, },
      loading: true,
    };
  },
  computed: {
    cssVars() {
      return {
        "--form-margin": this.style.margin + "%",
        "--form-width": this.style.width + "%",
      };
    },
    show: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    valid() {
      return this.slides.length && this.slides[this.currentSlide].valid;
    },
  },
  methods: {
    validateSlide: async function (index = this.currentSlide) {
      this.loading = true;
      let validates = this.slides[index].validate || [],
        valid = true;
      for (const { name, value, handler } of validates) {
        let validation = handler;
        switch (validation) {
          case "required":
            validation = this.required;
            break;
          case "unique":
            validation = this.unique;
            if (!this.mustBeUnique.hasOwnProperty(name))
              Vue.set(this.mustBeUnique, name, []);
            if (this.mustBeUnique[name].includes(value)) {
              Vue.set(this.formErrors, name, ["unique"]);
              this.loading = false;
              return false;
            }
            break;
          case "exists":
            validation = this.exists;
            break;
        }
        await validation({ name, value })
          .then(() => Vue.delete(this.formErrors, name))
          .catch((e) => {
            Vue.set(this.formErrors, name, e);
            valid = false;
            if (handler === "unique") {
              this.mustBeUnique[name].push(value);
            }
          });
      }
      this.loading = false;
      this.$emit("errors", this.formErrors);
      return valid;
    },
    // 
    moveToSlide: async function (index) {
      if (this.currentSlide < index)
        this.validateSlide().then((valid) => {
          if (valid) {
            this.$emit(`validated-${this.currentSlide}`);
            this.continueNavigating(index);
          } else this.slides[this.currentSlide].passed = false;
        });
      else this.continueNavigating(index);
    },
    continueNavigating(index) {
      if (
        (!Object.keys(this.formErrors).length &&
          index < this.slides.length &&
          index >= 0) ||
        this.slides[index].passed
      ) {
        this.setSlideTabIndexes(index);
        this.slides[this.currentSlide].passed = true;
        this.style.margin = -1 * 100 * index;
        this.currentSlide = index;
      }
    },
    // 
    required: function ({ value }) {
      return new Promise((resolve, reject) => {
        if (!value) reject(["required"]);
        else resolve();
      });
    },
    unique: function ({ name, value }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/check-unique-${name}`, { value })
          .then(() => resolve())
          .catch((err) => reject(err.response.data.errors["value"]));
      });
    },
    exists: function ({ name, value }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/check-exists-${name}`, { value })
          .then(() => resolve())
          .catch((err) => reject(err.response.data.errors["value"]));
      });
    },
    // 
    proceed: function () {
      this.loading = true;
      this.$emit("proceed");
    },
    cancel: function () {
      this.$emit("cancel");
      this.$bvModal
        .msgBoxConfirm(this.$t("dismissFormWarningMsg"), {
          title: this.$t("warning"),
          size: "sm",
          buttonSize: "sm",
          okVariant: "danger",
          okTitle: this.ucFirst(this.$t("dismiss")),
          cancelVariant: "primary",
          cancelTitle: this.ucFirst(this.$t("cancel")),
          footerClass: "p-2",
          hideHeaderClose: false,
          centered: true,
        })
        .then((value) => {
          if (value) {
            this.show = false;
          }
        });
    },
    // 
    setSlideTabIndexes: function (index) {
      index += 1;
      document
        .querySelectorAll(
          `
          .multiple-step-form > form > main > div .multiple-step-form-slide:not(:nth-child(${index})) input,
          .multiple-step-form > form > main > div .multiple-step-form-slide:not(:nth-child(${index})) select,
          .multiple-step-form > form > main > div .multiple-step-form-slide:not(:nth-child(${index})) textarea
          `
        )
        .forEach((input) => (input.tabIndex = "-1"));
      let i = 0;
      document
        .querySelectorAll(
          `
          .multiple-step-form > form > main > div .multiple-step-form-slide:nth-child(${index}) input,
          .multiple-step-form > form > main > div .multiple-step-form-slide:nth-child(${index}) select,
          .multiple-step-form > form > main > div .multiple-step-form-slide:nth-child(${index}) textarea
          `
        )
        .forEach((input) => (input.tabIndex = ++i));
    },
  },
  watch: {
    show: {
      handler: function (newVal) {
        if (newVal) {
          setTimeout(() => {
            this.slides = this.$children.filter(
              (child) =>
                child.$options._componentTag === "multiple-step-form-slide"
            );
            this.loading = false;
            this.style.width = this.slides.length * 100;
            document
              .querySelectorAll(".multiple-step-form input")
              .forEach((input) => (input.tabIndex = "-1"));
            document
              .querySelectorAll(".multiple-step-form select")
              .forEach((select) => (select.tabIndex = "-1"));
            this.setSlideTabIndexes(0);
          }, 500);
        }
      },
      immediate: true,
    },
    errors: function (newVal) {
      if (Object.keys(newVal).length) {
        this.loading = false;
        // move to slide that contains that error
        // this.moveToSlide(0);
      }
    },
    isLoading: function (newVal) {
      this.loading = newVal;
    },
  },
};
</script>

<style lang="scss">
@import "../../../../sass/variables";
@import "../../../../sass/functions";
@import "../../../../sass/mixins/dir";

.multiple-step-form {
  header {
    .header-icon {
      height: 2rem;
      width: 2rem;
      border-width: 0.125rem;

      &:not(:last-of-type) {
        position: relative;
        &::after {
          content: "";
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          height: 0.125rem;
          width: 1.25rem;
          background-color: root_prefix(light);
        }
      }

      &.passed:not(:last-of-type)::after {
        background-color: root_prefix(primary);
      }
    }
    .h-line {
      height: 0.125rem;
      width: 2.5rem;
      background-color: root_prefix(light);
    }
  }

  main {
    .dynamic {
      transition: margin root_prefix(duration-m) ease-in-out;
      width: var(--form-width);
    }
  }

  // footer {
  // }
}

@include dir {
  .multiple-step-form {
    header .header-icon {
      &:not(:last-of-type) {
        margin-#{$right}: 1.25rem;
      }
      &::after {
        #{$left}: calc(100% + 2px);
      }
    }
  }

  .dynamic {
    margin-#{$left}: var(--form-margin);
  }
}
</style>
