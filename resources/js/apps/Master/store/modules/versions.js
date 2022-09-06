const state = {
  versions: {},
}

const getters = {
  versions: state => state.versions,
}

const mutations = {
  STORE(state, version) {
    Vue.set(state.versions, version.id, version);
  },
  INDEX(state, versions) {
    versions.forEach(version => {
      this.commit('versions/STORE', version);
    });
  },
  CREATE(state, version) {
    this.commit('versions/STORE', version);
  },
  UPDATE(state, version) {
    this.commit('versions/STORE', version);
  },
  DELETE(state, id) {
    Vue.delete(state.versions, id);
  },
}

const actions = {
  index({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/versions')
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
        .get(`/api/v1/versions/${id}`)
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
      this.dispatch('images/prepareFormWithImg', { url: '/api/v1/versions', form })
        .then(({ data }) => {
          commit('CREATE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/versions/${form.id}`, form })
        .then(({ data }) => {
          commit('UPDATE', data);
          resolve();
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
        .delete(`/api/v1/versions/${id}`)
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
  //
  download({}, id) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/versions/${id}/download`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateFile({ }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/versions/files/${form.name}`, form })
        .then(() => resolve())
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateAllApps({ }, id) {
    return new Promise((resolve, reject) => {
      axios.get(`/api/v1/versions/${id}/update-all-apps`)
        .then(({ data }) => {
          this.commit('clients/SET_APPS_AS_UPDATING', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
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