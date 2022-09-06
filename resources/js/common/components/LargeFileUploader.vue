<template>
  <div :class="`form-control py-${file ? 3 : 1}`" style="height: auto !important">

    <div class="d-flex">
      <input type="file" :id="id" class="flex-1 form-control border-0 p-1" @change="select" />
      <b-button 
        v-if="file"
        class="bg-all-none border-0"
        size="sm"
        :variant="`outline-${is_uploaded ? 'danger' : 'secondary'}`"
        :icon="is_uploaded ? 'trash' : 'times'"
        @click="file = null"
      >
        {{ $t(is_uploaded ? 'delete' : 'cancel') }}
      </b-button>
    </div>

    <div v-if="file" class="mt-2 rounded-xl overflow-hidden bg-light border mx-1">
      <div :style="`height: 0.5rem; width: ${uploaded / max * 100}%; background-color: var(--bs-${variant});`" />
    </div>

  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'large-file-uploader',
  props: {
    value : { required: true },
  },
  data() {
    return {
      file: null,
      chunks: [],
      uploaded: 0,
    };
  },
  computed: {
    ...mapGetters({
      app: 'configurations/app',
    }),
    input: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
    id() {
      return `large-file-picker-${Math.floor(Math.random() * 1000000)}`;
    },
    chunk_size() {
      let app = this.app;
      return app && app.live ? 40960 : 1024000;
    },
    variant() {
      return !this.uploaded ? 'light' : this.uploaded == this.max ? 'success' : 'primary';
    },
    max() {
      return Math.max(this.uploaded, this.file ? this.file.size : 0);
    },
    formData() {
      if (!this.file || !this.chunks[0])
        return {};

      let formData = new FormData();

      formData.set("is_last", this.chunks.length === 1);
      formData.set("file", this.chunks[0], `${this.file.name}.part`);

      return formData;
    },
    config() {
      return {
        method: "POST",
        data: this.formData,
        url: "/api/v1/upload-large-file",
        headers: {
          "Content-Type": "application/octet-stream",
        },
        onUploadProgress: (event) => {
          this.uploaded += event.loaded;
        },
      };
    },
    is_uploaded() {
      return this.uploaded && this.uploaded >= this.max;
    },
  },
  methods: {
    select(event) {
      this.file = event.target.files.item(0);
      this.createChunks();
    },
    upload() {
      axios(this.config)
        .then(({ data }) => {
          this.chunks.shift();
          if (data && data.path)
            this.input = data.path;
        })
        .catch((error) => {});
    },
    createChunks() {
      let size = this.chunk_size,
        chunks = Math.ceil(this.file.size / size);

      for (let i = 0; i < chunks; i++) {
        this.chunks.push(
          this.file.slice(
            i * size,
            Math.min(i * size + size, this.file.size),
            this.file.type
          )
        );
      }
    },
  },
  watch: {
    chunks(n, o) {
      if (n.length > 0) {
        this.upload();
      }
    },
    file(file) {
      if (!file) {
        this.uploaded = 0;
        document.getElementById(this.id).value = null;
      }
    },
  },
};
</script>