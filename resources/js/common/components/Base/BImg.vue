<template>
  <div
    class="b-img d-flex-center"
    :style="cssVars"
    :class="[{ 'rounded-circle overflow-hidden': circle === '' }, `bg-${bg}`]"
  >
    <img
      :src="`${imgSrc}`"
      :alt="alt || placeholder || 'Photo'"
      :class="`${imgClass} ${size == '100%' ? 'w-100 h' : longerSize}-100`"
      :style="imgStyle"
    />
  </div>
</template>

<script>
export default {
  name: "b-img",
  props: {
    size              : { default: 50 },
    src               : { type: String },
    alt               : { type: String, default: "loading" },
    imgClass          : { default: "" },
    imgStyle          : { default: "" },
    placeholderType   : { default: "user" },
    circle            : { default: null },
    bg                : { default: 'none'}
  },
  data() {
    return {
      longerSize: 'h',
    };
  },
  mounted: async function () {
    const img = document.createElement('img');
    img.onload = () => this.longerSize = img.naturalWidth > img.naturalHeight ? 'w' : 'h';
    img.src = this.src;
  },
  computed: {
    cssVars() {
      if (this.size != '100%') {
        var height = null,
          width = null;
        if (this.size.toString() === "fill") {
          if(this.longerSize == 'h') {
            height = "auto";
            width = "100%";
          } else {
            height = "100%";
            width = "auto";
          }
        } else if (this.size.toString().indexOf("x") !== -1) {
          const i = this.size.toString().indexOf("x");
          height = this.size.substr(0, i) / 16 + "rem";
          width = this.size.substr(i + 1) / 16 + "rem";
        } else {
          height = this.size / 16 + "rem";
          width = this.size / 16 + "rem";
        }
      }
      return {
        "--height": height || this.size,
        "--width": width || this.size,
      };
    },
    imgSrc() {
      let placeholder = `/imgs/placeholders/${this.placeholderType}.png`;
      return !this.src || this.src.indexOf("undefined") !== -1 || this.src.indexOf("null") !== -1 ? placeholder : this.src;
    },
  },
};
</script>

<style lang="scss" scoped>
.b-img {
  height: var(--height);
  width: var(--width);
  overflow: hidden;
  box-sizing: content-box;
}
.bg-black {
  background-color: var(--bs-black);
}
</style>