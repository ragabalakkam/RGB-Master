const state = { }

const getters = { }

const mutations = {
  STORE(state, { module, data }) {
    Vue.set(state[module], data.id, data);
  },
  INDEX(state, { module, data }) {
    state[module] = {};
    data.forEach(value => {
      this.commit('modules/STORE', { module, data: value });
    });
  },
  CREATE(state, { module, data }) {
    this.commit('modules/STORE', { module, data });
  },
  UPDATE(state, { module, data }) {
    this.commit('modules/STORE', { module, data });
  },
  DELETE(state, { module, id }) {
    Vue.delete(state[module], id);
  },
}

const actions = {
  index({ commit }, { module }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/${module}`)
        .then(({ data }) => {
          commit('INDEX', { module, data });
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  find({ commit }, { module, id }) {
    return new Promise(async (resolve, reject) => {
      axios
        .get(`/api/v1/${module}/${id}`)
        .then(({ data }) => {
          commit('STORE', { module, data });
          resolve(this._vm.obj_clone(module));
        })
        .catch(() => reject(null));
    });
  },
  create({ commit }, { module, form }) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/${module}`, form })
        .then(({ data }) => {
          commit('CREATE', { data, module });
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  update({ commit }, { module, form }) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/${module}/${form.id}`, form })
        .then(({ data }) => {
          commit('UPDATE', { module, data });
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  delete({ commit }, { module, id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/${module}/${id}`)
        .then(() => {
          commit('DELETE', { module, id });
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  get({}, module) {
    return state[module];
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}