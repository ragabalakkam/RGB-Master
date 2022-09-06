const state = {
  clients: {},
  client_apps: {},
  commands: {},
  get_client_app_channel_name: app => `Master.Clients.${app.client_id}.${app.id}`,
}

const getters = {
  clients: state => state.clients,
  client_apps: state => state.client_apps,
  commands: state => state.commands,
}

const mutations = {
  STORE(state, client) {
    if (client.client_apps) {
      let apps = {};
      client.client_apps.forEach(ca => {
        this.commit('clients/APP_STORE', ca);
        apps[ca.id] = ca;
      });
      client.client_apps = apps;
    }
    Vue.set(state.clients, client.id, client);
  },
  INDEX(state, clients) {
    clients.forEach(client => this.commit('clients/STORE', client));
  },
  CREATE(state, client) {
    this.commit('clients/STORE', client);
  },
  UPDATE(state, client) {
    this.commit('clients/STORE', client);
  },
  DELETE(state, id) {
    Vue.delete(state.clients, id);
  },
  // 
  APP_STORE(state, client_app) {
    Vue.set(state.client_apps, client_app.id, client_app);
  },
  APP_INDEX(state, client_apps) {
    client_apps.forEach(client_app => this.commit('clients/APP_STORE', client_app));
  },
  APP_CREATE(state, client_app) {
    this.commit('clients/APP_STORE', client_app);
  },
  APP_UPDATE(state, client_app) {
    this.commit('clients/APP_STORE', client_app);
  },
  APP_DELETE(state, id) {
    Vue.delete(state.client_apps, id);
  },
  // 
  REMOVE_IMAGE(state, client_id) {
    Vue.set(state.clients[client_id], 'image', null);
  },
  SET_APP_INSTALLED_AT(state, { id, client_id, installed_at = null }) {
    let client = state.clients[client_id];
    let index = client ? client.client_apps.findIndex(client_app => client_app.id == id) : null;

    client.client_apps[index].installed_at = installed_at || null;
    client.client_apps[index].started_process_at = null;
    client.client_apps[index].active_process = null;
    
    this.commit('clients/APP_STORE', client.client_apps[index]);
    this.commit('clients/STORE', client);
  },
  SET_APPS_AS_UPDATING(state, ids) {
    ids.forEach(id => state.client_apps.hasOwnProperty(id) ? Vue.set(state.client_apps[id], 'active_process', 'update') : null);
  },
  //
  OUTPUT_RECEIVED(state, output) {
    if (!state.commands.hasOwnProperty(output.app_id))
      Vue.set(state.commands, output.app_id, {});

    Vue.set(state.commands[output.app_id], output.id, { ...(state.commands[output.app_id][output.id] || {}), ...output });
  },
  CLEAR_CONSOLE(state, id) {
    Vue.set(state.commands, id, {});
  },
}

const actions = {
  index({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/clients')
        .then(({ data }) => {
          commit('INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  find({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/clients/${id}`)
        .then(({ data }) => {
          commit('STORE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  create({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: '/api/v1/clients', form })
        .then(({ data }) => {
          commit('CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/clients/${form.id}`, form })
        .then(({ data }) => {
          commit('UPDATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  delete({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/clients/${id}`)
        .then(() => {
          commit('DELETE', id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  fetchBackups({ }) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/clients/backups`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });

  },
  // 
  remove_image({ commit }, id) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/clients/${id}/remove-image`)
        .then(() => {
          commit('REMOVE_IMAGE', id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  modify_app_installation_status({ commit }, { id, value }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/${value ? 'install' : 'uninstall'}`)
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  modify_app_license_status({ commit }, { id, value }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/${value ? 'license' : 'unlicense'}`)
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update_app_business_type({ commit }, { id, business_type_id, configurations, update_configurations, update_database }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/update-business-type`, { business_type_id, configurations, update_configurations, update_database })
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update_app_configurations({ commit }, { id, configurations }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/update-configurations`, { configurations })
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update_app_domain({ commit }, { id, domain, root_dir }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/update-domain`, { domain, root_dir })
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update_app_database({ commit }, { id, db_driver, db_host, db_database, db_username, db_password }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/update-database`, { db_driver, db_host, db_database, db_username, db_password })
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  export_app_database({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/client-apps/${id}/export-database`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  import_app_database({ commit }, { id, file }) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/client-apps/${id}/import-database`, form: { file } })
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  clean_app_database({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/client-apps/${id}/clean-database`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update_app_version({ commit }, { id, version_id }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/client-apps/${id}/update-version`, { version_id })
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  check_app_for_update({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/client-apps/${id}/check-for-updates`)
        .then(({ data }) => {
          commit('APP_STORE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },

  // Customer Support
  check_console_status: function ({ commit }, id) {
    return new Promise((resolve, reject) => {
      axios
        .post(`/api/v1/client-apps/${id}/check-status`)
        .then(({ data }) => resolve(data.online))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  execute_console_command({ commit }, { id, command }) {
    return new Promise((resolve, reject) => {
      axios
        .post(`/api/v1/client-apps/${id}/execute-command`, { command })
        .then(({ data }) => {
          commit('OUTPUT_RECEIVED', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  clear_console({ commit }, id) {
    commit('CLEAR_CONSOLE', id);
  },
  startListeningToSupport: function ({ commit }, app) {
    // listen to channel
    const app_channel_name = state.get_client_app_channel_name(app);
    state.channel = window.Echo.private(app_channel_name);

    // defining listeners
    state.channel.listen('.ClientResponse', output => commit('OUTPUT_RECEIVED', output));
  },
  stopListeningToSupport: function ({ commit }, app) {
    const app_channel_name = state.get_client_app_channel_name(app);
    window.Echo.leave(app_channel_name);
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}