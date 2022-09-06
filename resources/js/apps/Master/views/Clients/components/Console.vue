<template>
  <div
    class="console-tab d-flex flex-column text-right text-light-4"
    :class="`max-ht-${hide_output ? '40' : '500 min-ht-150'} bg-${variant}-5`"
    dir="ltr"
  >
    <div
      class="d-flex rounded-top overflow-hidden c-ptr"
      :class="`bg-${variant}`"
      @click="hide_output = !hide_output"
    >
      <b-button :variant="variant" icon="window" class="flex-1 rounded-0 px-3 py-2 text-right">
        <span class="mx-2">Console</span>
      </b-button>

      <!-- status -->
      <div v-if="!loading" class="text-right p-2" v-text="`(${online ? 'connected' : 'not connected'})`" />

      <!-- tools -->
      <b-button :variant="variant" class="px-3 py-2 rounded-0" icon="broom" @click.stop="clear" />
      <b-button v-if="!loading" :variant="variant" class="px-3 py-2 rounded-0" icon="redo" @click.stop="check_status" />
      <beat-loader v-else color="white" size="3px" class="m-2 text-right" />
    </div>

    <form
      class="flex-1 bd-blur-3 px-3"
      :class="[{ 'py-2': !hide_output }, `bg-${variant}-8`]"
      @submit.prevent="execute"
      @click="foucs"
    >
      <template v-if="online">
        <div class="d-flex flex-gap-2 align-items-center">
          <p>command : </p>
          <b-input
            id="console-command"
            class="flex-1"
            input-class="bg-all-none border-0 text-light text-focus-white text-hover-white p-0"
            v-model="command"
          />
        </div>
        <template v-if="commands">
          <div v-for="cmd in commands" :key="cmd.id" class="border-top border-light-3 pt-2">
            <p class="text-light-7">
              <span v-text="`[${cmd.created_at}]`" />
              <span v-text="cmd.command" class="c-ptr text-hover-light" @click="command = cmd.command" />
              <span>></span>
            </p>
            <p>
              <span v-text="`[${cmd.responded_at || 'waiting ..'}]`" />
              <span v-text="cmd.output" />
            </p>
          </div>
          <beat-loader v-show="loading" color="white" size="3px" class="mt-2 text-right" />
        </template>
      </template>
      <p v-else>App is offline</p>
    </form>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'console',
  props: {
    app : { required: true, type: Object },
  },
  data() {
    return {
      hide_output: true,
      command: null,
      online: false,
      loading: true,
    };
  },
  computed: {
    ...mapGetters({
      all_commands: 'clients/commands',
    }),
    variant() {
      return this.online ? 'dark' : 'secondary';
    },
    commands() {
      return this.all_commands[this.app.id] || {};
    },
  },
  methods: {
    clear: function () {
      this.$store.dispatch('clients/clear_console', this.app.id);
    },
    foucs: function () {
      document.getElementById('console-command') ? document.getElementById('console-command').focus() : null
    },
    check_status: async function () {
      this.loading = true;
      await this.$store
        .dispatch('clients/check_console_status', this.app.id)
        .then(online => this.online = online)
        .catch(err => console.log({ err }));
      this.loading = false;
    },
    execute: async function () {
      this.loading = true;
      await this.$store
        .dispatch('clients/execute_console_command', { id: this.app.id, command: this.command })
        .then(() => this.command = null)
        .catch(err => console.log({ err }));
      this.loading = false;
    },
  },
  mounted: function () {
    this.check_status();
    this.foucs();
  },
  destroyed: async function () {
    this.loading = true;
    await this.$store.dispatch('clients/stopListeningToSupport', this.app);
    this.loading = false;
  },
  watch: {
    online: async function (online) {
      if(online) {
        this.loading = true;
        await this.$store.dispatch('clients/startListeningToSupport', this.app);
        this.loading = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.console-tab {
  width: 50rem;

  > div {
    flex: none;
  }

  form {
    overflow-y: scroll;
    font-family: monospace;
  }
}
</style>