<template>
  <b-form-group :disabled="disabled" @click="e => { $emit('click', e); input = !input }" :label="label">
    <div class="input-on-off d-flex align-items-center no-select" :class="`c-${disabled ? 'not-allowed' : 'ptr'}`">
      <input type="checkbox" class="d-none" v-model="input" />

      <label
        :class="`lbl-${size || 'lg'} border-${input ? 'primary' : 'secondary-3'}`"
        class="lbl-on-off bg-white c-ptr mr-2 border rounded-edges mb-0"
      />

      <div class="flex-1" :class="`text-${input ? (onColor || 'primary') : (offColor || 'secondary')} font-${size || 'lg'}`">
        <slot />
      </div>
    </div>
  </b-form-group>
</template>

<script>
export default {
  name: "input-on-off",
  props: ["value", "size", "disabled", "label", "onColor", "offColor"],
  computed: {
    input: {
      get() {
        return this.value;
      },
      set(x) {
        this.$emit("input", x);
      },
    },
  },
};
</script>

<style lang="scss">
@import "../../../../sass/mixins/dir";
.input-on-off {
  .lbl-on-off {
    position: relative;

    &::after {
      content: "";
      position: absolute;
      top: 0.125rem;
      border-radius: 50%;
      transition: 0.2s ease-in-out;
      background-color: var(--bs-secondary);
      opacity: 0.4;
    }

    &.lbl-sm {
      width: 1.5625rem;
      height: 0.9375rem;

      &::after {
        height: 0.5625rem;
        width: 0.5625rem;
      }
    }

    &.lbl-md {
      width: 1.5625rem;
      height: 1rem;

      &::after {
        height: 0.625rem;
        width: 0.625rem;
      }
    }

    &.lbl-lg {
      width: 1.875rem;
      height: 1.0625rem;

      &::after {
        height: 0.6875rem;
        width: 0.6875rem;
      }
    }

    &.lbl-xl {
      width: 2.1875rem;
      height: 1.25rem;

      &::after {
        height: 0.875rem;
        width: 0.875rem;
      }
    }
  }

  input[type="checkbox"]:checked + .lbl-on-off::after {
    background-color: var(--bs-info);
    opacity: 1;
  }

  .flex-1 {
    line-height: 1.5rem;
  }
}

@include dir {
  .input-on-off {
    .lbl-on-off::after {
      #{$left}: 0.125rem;
    }

    input[type="checkbox"]:checked + .lbl-on-off.lbl-sm::after {
      #{$left}: 0.85rem;
    }

    input[type="checkbox"]:checked + .lbl-on-off.lbl-md::after {
      #{$left}: 0.85rem;
    }

    input[type="checkbox"]:checked + .lbl-on-off.lbl-lg::after {
      #{$left}: 0.85rem;
    }

    input[type="checkbox"]:checked + .lbl-on-off.lbl-xl::after {
      #{$left}: 1.0625rem;
    }
  }
}
</style>