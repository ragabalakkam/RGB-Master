<template>
  <div
    v-if="imageToView"
    ref="imageViewer"
    class="image-viewer position-absolute position-top-left bg-black-9 min-vh-100 min-vw-100"
    @click="close"
  >
    <div class="container min-vh-100 d-flex-center">
      <img
        ref="imageViewed"
        :style="`visibility: ${loading ? 'hidden' : 'visible'}; ${imageViewedContainerStyle}`"
        :src="imageToView"
        class="d-block"
        @click.stop
      />
      <!-- top right options -->
      <div
        class="options position-absolute position-top-right mt-4 mr-4 h4"
        v-show="!loading"
      >
        <!-- close -->
        <b-button
          variant="light"
          class="bg-none border-0 p-0 size-40 d-flex-center text-light-5 text-hover-white"
          @click.stop="close"
          block
          :title="$t('imageViewer.close')"
        >
          <b-i icon="times" />
        </b-button>
        <!-- download -->
        <a
          @click.stop
          class="size-40 d-flex-center text-light-5 text-hover-white"
          :href="imageToView"
          :title="$t('imageViewer.download')"
          download
        >
          <b-i icon="download" />
        </a>
      </div>
      <clip-loader v-show="loading" />
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "image-viewer",
  data() {
    return {
      biggerSide: null,
      loading: false,
      imageViewedContainerStyle: null,
    };
  },
  computed: {
    ...mapGetters({
      imageToView: "imageToView",
    }),
    maxWidth() {
      return this.wXS ? 300 : this.wSM ? 500 : 900;
    },
    maxHeight() {
      return this.wXS ? 300 : this.wSM ? 500 : 500;
    },
    width() {
      return this.wXS ? 90 : this.wSM ? 75 : 65;
    },
    height() {
      return this.wXS ? 85 : this.wSM ? 50 : 30;
    },
  },
  methods: {
    detectBiggerSide: function () {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
          const width = img.naturalWidth,
            height = img.naturalHeight;
          // detecting bigger side
          const biggerSide = height > width ? "h" : "w";
          this.biggerSide = biggerSide;
          // if height is bigger than max
          if (height > this.maxHeight)
            this.imageViewedContainerStyle = `height: ${this.height}vh`;
          else this.imageViewedContainerStyle = null;
          // if width is bigger than max
          if (width > this.maxWidth)
            this.imageViewedContainerStyle = `width: ${this.width}vw`;
          else this.imageViewedContainerStyle = null;
        };
        img.src = this.imageToView;
        resolve();
      });
    },
    close: function () {
      this.$store.commit("SET_IMG_TO_VIEW", null);
    },
  },
  watch: {
    imageToView: async function (newVal) {
      if (!!newVal) {
        this.loading = true;
        this.detectBiggerSide();
        this.loading = false;
      }
    },
    wXS: "detectBiggerSide",
    wSM: "detectBiggerSide",
    wLG: "detectBiggerSide",
    WXL: "detectBiggerSide",
  },
};
</script>

<style lang="scss" scoped>
.image-viewer {
  z-index: 99999;
  img {
    box-shadow: 0 0 40px black;
  }

  .size-40 {
    width: 2.5rem;
    height: 2.5rem;
  }

  .btn {
    font-size: inherit;
  }
}
</style>