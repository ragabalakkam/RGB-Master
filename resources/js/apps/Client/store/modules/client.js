const state = {
  apps: {},
  client_apps: {},
  organizations: {},
}

const getters = {
  apps: state => state.apps,
  client_apps: state => state.client_apps,
  organizations: state => state.organizations,
}

const mutations = {
  // organizations
  ORGANIZATION_STORE(state, organization) {
    Vue.set(state.organizations, organization.id, organization);
  },
  ORGANIZATION_INDEX(state, organizations) {
    organizations.forEach(organization => this.commit('client/ORGANIZATION_STORE', organization));
  },
  ORGANIZATION_CREATE(state, organization) {
    this.commit('client/ORGANIZATION_STORE', organization);
  },
  ORGANIZATION_UPDATE(state, organization) {
    this.commit('client/ORGANIZATION_STORE', organization);
  },
  ORGANIZATION_DELETE(state, id) {
    Vue.delete(state.organizations, id);
  },
  
  // apps
  APP_STORE(state, app) {
    Vue.set(state.apps, app.id, app);
  },
  APP_INDEX(state, apps) {
    apps.forEach(app => this.commit('client/APP_STORE', app));
  },
  APP_CREATE(state, organization) {
    this.commit('client/APP_STORE', organization);
  },
  
  // client-apps
  CLIENT_APP_STORE(state, client_app) {
    Vue.set(state.client_apps, client_app.id, client_app);
  },
  CLIENT_APP_INDEX(state, apps) {
    apps.forEach(app => this.commit('client/CLIENT_APP_STORE', app));
  },
  CLIENT_APP_CREATE(state, client_app) {
    this.commit('client/CLIENT_APP_STORE', client_app);
  },
  CLIENT_APP_UPDATE(state, client_app) {
    this.commit('client/CLIENT_APP_STORE', client_app);
  },
  CLIENT_APP_DELETE(state, id) {
    Vue.delete(state.client_apps, id);
  },
}

const actions = {
  // organizations
  fetchOrganizations({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/organizations`)
        .then(({ data }) => {
          commit('ORGANIZATION_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  findOrganization({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/organizations/${id}`)
        .then(({ data }) => {
          commit('ORGANIZATION_CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createOrganization({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/organizations`, form })
        .then(({ data }) => {
          commit('ORGANIZATION_CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateOrganization({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/organizations/${form.id}`, form })
        .then(({ data }) => {
          commit('ORGANIZATION_UPDATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteOrganization({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/organizations/${id}`)
        .then(() => {
          commit('ORGANIZATION_DELETE', id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  
  // apps
  fetchApps({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/apps`)
        .then(({ data }) => {
          commit('APP_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  findApp({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/apps/${id}`)
        .then(({ data }) => {
          commit('APP_CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  
  // client-apps
  fetchClientApps({ commit }, id) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/organizations/${id}/apps`)
        .then(({ data }) => {
          commit('CLIENT_APP_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  findClientApp({ commit }, { org_id, id }) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/organizations/${org_id}/apps/${id}`)
        .then(({ data }) => {
          commit('CLIENT_APP_CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createClientApp({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/organizations/${form.client_id}/apps`, form })
        .then(({ data }) => {
          commit('CLIENT_APP_CREATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateClientApp({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/organizations/${form.client_id}/apps/${form.id}`, form })
        .then(({ data }) => {
          commit('CLIENT_APP_UPDATE', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteClientApp({ commit }, { org_id, id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/organizations/${org_id}/apps/${id}`)
        .then(() => {
          commit('CLIENT_APP_DELETE', id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },

  // tools
  update_app_business_type({ commit }, { id, business_type_id }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/client-apps/${id}/update-business-type`, { business_type_id, configurations: [], update_configurations: null, update_database: null })
        .then(({ data }) => {
          commit('SET_CLIENT_APP', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  export_app_database({}, { id }) {
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

  // 
  reportProblem: function ({}, form) {
    return new Promise(async (resolve, reject) => {
      const config = { headers: { 'content-type': 'multipart/form-data' } };
      let formdata = new FormData();
      Object.entries(form).forEach(entry => entry[1] ? formdata.append(entry[0], entry[1]) : null);
      axios.post('/api/v1/report-bug', formdata, config)
				.then(({ data }) => resolve(data))
				.catch(errors => reject(errors.response.data.errors));
    });
  },
  contact: function ({}, form) {
    return new Promise(async (resolve, reject) => {
      const config = { headers: { 'content-type': 'multipart/form-data' } };
      let formdata = new FormData();
      Object.entries(form).forEach(entry => entry[1] ? formdata.append(entry[0], entry[1]) : null);
      axios.post('/api/v1/contact', formdata, config)
				.then(({ data }) => resolve(data))
				.catch(errors => reject(errors.response.data.errors));
    });
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}