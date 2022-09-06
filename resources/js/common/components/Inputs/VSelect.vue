<template>
  <b-form-group>
    <div class="position-relative">
      <b-input
        v-bind="$attrs"
        v-model="search"
        :list="`datalist-${_id}`"
        class="mb-0"
        :errors="errors"
        :name="name"
        :attr="attr"
        input-class="custom-select"
        :placeholder="_castNullOption(nullOptionAttr)"
        onClick="this.value ? this.setSelectionRange(0, this.value.length) : null"
        @keyup="typeof data != 'string' ? searchApi : null"
      />
      <clip-loader v-show="loading" size="14px" class="position-absolute text-light position-bottom-right mr-3 mb-1" />
    </div>

    <datalist v-if="!loading" :id="`datalist-${_id}`">
      <option
        v-for="option in options"
        :key="option[val]"
        :value="option.text"
        :disabled="option.disabled"
      />
      <option
        v-if="search && !match && !input"
        :value="castNoMatches ? castNoMatches(search) : $t('noMatches', { attr: search })"
      />
    </datalist>

    <b-input type="hidden" v-model="input" />
  </b-form-group>
</template>

<script>
export default {
  name: "v-select",
  props: {
    value               : { default: null },
    data                : { default: null },
    id                  : { default: null },
    val                 : { default: "id" },
    txt                 : { default: "name" },
    name                : { required: false },
    attr                : { required: false },
    errors              : { required: false },
    castText            : { default: () => (x) => x },
    castValue           : { default: () => (x) => x },
    castData            : { default: () => (x) => x },
    castNullOption      : { default: null },
    castNoMatches       : { default: null },
    nullOptionAttr      : { default: null },
    nullOptionDisabled  : { default: true },
    clearAfterSelect    : { default: false },
    commit              : { default: null },
  },
  data() {
    return {
      search: null,
      api_data: {},
      timeout: null,
      loading: false,
    };
  },
  computed: {
    input: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value;
      },
    },
    _id() {
      return this.id || `${Math.ceil(Math.random() * 10000)}`;
    },
    _select_data() {
      return typeof this.data == 'string' ? this.castData(this.api_data) : this.data;
    },
    nullOption() {
      const attr = this.nullOptionAttr;
      return attr
        ? this.castNullOption
          ? this.castNullOption(attr)
          : `-- ${this.$t("selectX", { attr })} --`
        : null;
    },
    _castNullOption() {
      return this.castNullOption || ((attr) => `-- ${this.$t("selectX", { attr })} --`);
    },
    options() {
      let options = [];
      if (this.nullOptionAttr)
        options.push({
          text  : (this._castNullOption(this.nullOptionAttr)).toLowerCase(),
          value : null,
          disabled: this.nullOptionDisabled,
        });
      Object.values(this._select_data || {}).map((d) => {
        options.push({
          text  : (this.castText ? this.castText(d[this.txt], d) : d[this.txt]).toLowerCase(),
          value : this.castValue ? this.castValue(d[this.val], d) : d[this.val],
        });
      });
      return options;
    },
    optionsByValues() {
      let options = {};
      this.options.forEach((opt) => (options[opt.value] = opt));
      return options;
    },
    match() {
      let search = this.search ? this.search.toLowerCase() : null;
      return this.options.find((op) => op.text.includes(search));
    },
  },
  methods: {
    searchApi: async function () {
      if (this.timeout) clearTimeout(this.timeout);
      this.timeout = setTimeout(async () => {
        this.loading = true;
        await axios.get(`/api/v1/${this.data}`, { params: { value: (this.search || '').split('--')[0] } })
          .then(({ data }) => {
            this.api_data = data;
            if (this.commit) this.$store.commit(this.commit, data);
          })
          .catch(errors => console.log({ errors }));
        this.loading = false;
      }, 500);
    },
  },
  watch: {
    search: function (text) {
      text = text ? text.toLowerCase() : null;
      let option = this.options.find((op) => op.text == text);
      this.input = option ? option.value : null;

      let noMatchText = this.castNoMatches ? this.castNoMatches('') : this.$t('noMatches', { attr: "" });
      noMatchText = noMatchText.substring(0, noMatchText.length - 5);

      if (text && !this.match && text.includes(noMatchText)) 
      {
        this.$emit("null-option-selected", text.match(/\(([^)]+)\)/)[1].trim());
        this.input = null;
        this.search = null;
      }

      if (option) this.search = this.clearAfterSelect ? null : option.text;

      this.$emit('search', text);
    },
    input: {
      handler: function (val) {
        this.$emit('change', val);
        let option = val ? this.options.find(op => op.value == val) : null;
        this.search = option ? option.text : null;
      },
      immediate: true,
    },
    x_options: function (options) {
      let option = this.input ? options.find(op => op.value == this.input) : null;
      this.search = option ? option.text : null;
    },
  },
};
</script>