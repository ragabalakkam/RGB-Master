<template>
  <input type="file" class="form-control h-100" @change="selectFile" />
</template>

<script>
export default {
  name: "b-file-input",
  props: {
    value: { required: true },
  },
  computed: {
    file: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
  },
  methods: {
    selectFile: function (e) {
      this.file = e.target && e.target.files ? e.target.files[0] : null;
    },
  },
};
</script>