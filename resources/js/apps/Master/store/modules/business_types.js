const state = {
  business_types: {},
  business_types_fetched: false,
}

const getters = {
  business_types: state => state.business_types,
}

const mutations = {
  STORE(state, type) {
    Vue.set(state.business_types, type.id, type);
  },
  INDEX(state, business_types) {
    state.business_types = {};
    business_types.forEach(business_type => {
      this.commit('business_types/STORE', business_type);
    });
    state.business_types_fetched = true;
  },
  CREATE(state, business_type) {
    this.commit('business_types/STORE', business_type);
  },
  UPDATE(state, business_type) {
    this.commit('business_types/STORE', business_type);
  },
  DELETE(state, id) {
    Vue.delete(state.business_types, id);
  },
}

const actions = {
  index({ commit }, forced = false) {
    if (state.business_types_fetched && !forced) return;
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/business-types')
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
  find({ dispatch }, id) {
    return new Promise(async (resolve, reject) => {
      await dispatch('index');
      let business_type = state.business_types[id] || null;
      business_type && !business_type.deleted_at ? resolve(this._vm.obj_clone(business_type)) : reject(null);
    });
  },
  create({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: '/api/v1/business-types', form })
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
      this.dispatch('images/prepareFormWithImg', { url: `/api/v1/business-types/${form.id}`, form })
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
        .delete(`/api/v1/business-types/${id}`)
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
  apply({ }, id) {
    return new Promise((resolve, reject) => {
      axios.post(`/api/v1/business-types/${id}/apply`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  export({ }, id) {
    return new Promise((resolve, reject) => {
      axios.get(`/api/v1/business-types/${id}/export`)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  import({ commit }, form) {
    return new Promise((resolve, reject) => {
      this.dispatch('images/prepareFormWithImg', { url: '/api/v1/business-types/import', form })
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
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}