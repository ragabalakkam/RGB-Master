const state = {
  apps: {},
}

const getters = {
  apps: state => state.apps,
}

const mutations = {
  STORE(state, app) {
    Vue.set(state.apps, app.id, app);
  },
  INDEX(state, apps) {
    apps.forEach(app => {
      this.commit('apps/STORE', app);
    });
  },
  CREATE(state, app) {
    this.commit('apps/STORE', app);
  },
  UPDATE(state, app) {
    this.commit('apps/STORE', app);
  },
  DELETE(state, id) {
    Vue.delete(state.apps, id);
  },
}

const actions = {
  index({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/apps')
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
        .get(`/api/v1/apps/${id}`)
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
      this.dispatch('images/prepareFormWithImg', { url: '/api/v1/apps', form })
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
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/apps/${form.id}`, form })
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
        .delete(`/api/v1/apps/${id}`)
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
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}