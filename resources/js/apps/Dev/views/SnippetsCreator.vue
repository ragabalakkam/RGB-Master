<template>
  <div class="snippets-creator h-100 overflow-scroll py-4">
    <div class="container">
      <div class="bg-white border rounded-lg px-4 py-5">
        <b-form-input v-model="variable" class="mb-3" />
        <div>
          <form @submit.prevent="submit" @reset.prevent="reset" class="bg-white">
            <b-textarea v-model="template" class="mb-3" :rows="15" />
            <div class="d-flex">
              <b-button type="submit" variant="primary"> submit </b-button>
              <b-button type="reset" variant="light" class="ml-2">
                reset
              </b-button>
            </div>
          </form>
        </div>
        <div class="mt-5" v-if="lines.length">
          <hr />
          <p>Snippet</p>
          <div class="bg-light border p-3" v-copy-on-click>
            <p>[</p>
            <p v-for="(line, index) in lines" :key="index">"{{ line }}",</p>
            <p>]</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "snippets-creator",
  data() {
    return {
      lines: [],
      template: "",
      variable: "key",
    };
  },
  methods: {
    submit: function () {
      this.lines = this.template
        .replaceAll("  ", "\\t")
        .replaceAll(this.variable, "${1:" + this.variable + "}")
        .split("\n");
    },
    reset: function () {
      this.lines = [];
    },
  },
};
</script>