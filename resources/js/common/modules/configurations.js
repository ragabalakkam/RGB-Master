import axios from "axios";

const state = {
  app: {},
  apps: {},
  paths: {},
  modules: {},
  cashier_settings: {},
}

const getters = {
  app : state => state.app,
  apps: state => state.apps,
  paths: state => state.paths,
  modules: state => state.modules,
  cashier_settings: state => state.cashier_settings,
}

const mutations = {
  STORE_CONFIGURATION(state, { key, value }) {
    Vue.set(state, key, value);
  },
  STORE_ENCRYPTED_CONFIGURATION(state, config) {
    config = JSON.parse(atob(config));
    this.commit('configurations/STORE_CONFIGURATION', config);
  },
  FETCH_CONFIGURATIONS(state, configurations) {
    configurations.forEach(config => {
      this.commit('configurations/STORE_CONFIGURATION', config);
    });
  },
}

const actions = {
  fetch({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get('/@cache/configurations.json')
        .then(({ data }) => {
          commit('FETCH_CONFIGURATIONS', data);
          resolve(data);
        })
        .catch(() => {
          axios
            .get(`/api/v1/configurations`)
            .then(({ data }) => {
              commit('FETCH_CONFIGURATIONS', data);
              resolve(data);
            })
            .catch(errors => {
              this.commit('HANDLE_ERRORS', errors);
              reject(errors);
            });
        });
    });
  },
  show({ commit }, key) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/configurations/${key}`)
        .then(async ({ data }) => {
          await commit('STORE_ENCRYPTED_CONFIGURATION', data);
          resolve(JSON.parse(atob(data)));
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors);
        });
    });
  },
  update({ commit }, { key, value }) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/configurations/${key}`, { key, value })
        .then(({ data }) => {
          commit('STORE_CONFIGURATION', data);
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