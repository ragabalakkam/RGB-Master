<template>
  <div
    class="form-control p-0 d-flex-center onhover c-ptr overflow-hidden position-relative"
    :style="height ? `height: ${height}` : 'auto'"
    @click="$refs.lbl.click()"
  >
    <img
      :src="src"
      :id="`img-${randNum}`"
      v-show="showImg || src"
      :class="imgClass || `${wXS ? 'w' : 'h'}-100`"
      class="position-absolute position-center"
      alt="input-image"
    />
    <b-i
      icon="image"
      size="3x"
      class="text-secondary-2 text-parent-hover-secondary-5"
      v-show="!showImg && !src"
    />
    <input
      :id="_id"
      type="file"
      accept="image/x-png,image/gif,image/jpeg"
      @change="inputChanged"
      class="d-none"
    />
    <label :for="_id" ref="lbl"></label>
  </div>
</template>

<script>
import { fillWithImg } from "../../../common/methods";
export default {
  name: "b-img-input",
  props: ["value", "height", "src", "id", "imgClass"],
  data() {
    return {
      randNum: Math.floor(Math.random() * 10000000),
      showImg: false,
    };
  },
  computed: {
    image: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    _id() {
      return this.id || `img-input-${this.randNum}`;
    },
  },
  methods: {
    inputChanged: function (e) {
      let file = e.target.files[0];
      const validImageTypes = ["image/gif", "image/jpeg", "image/png"];
      if (file && validImageTypes.includes(file["type"])) {
        fillWithImg(e.target, $(`#img-${this.randNum}`));
        this.showImg = true;
        this.image = file;
        setTimeout(() => this.$emit('change', document.getElementById(`img-${this.randNum}`).src), 50);
      } else {
        this.showImg = false;
        this.image = null;
      }
    },
  },
};
</script>