const state = {
  employees: {},
}

const getters = {
  employees: state => state.employees,
}

const mutations = {
  STORE_INTO_STATE(state, employee) {
    Vue.set(state.employees, employee.id, employee);
  },
  INDEX(state, employees) {
    employees.forEach(employee => this.commit('employees/STORE_INTO_STATE', employee));
  },
  CREATE(state, employee) {
    this.commit('employees/STORE_INTO_STATE', employee);
  },
  UPDATE(state, employee) {
    this.commit('employees/STORE_INTO_STATE', employee);
  },
  DELETE(state, id) {
    Vue.delete(state.employees, id);
  },
}

const actions = {
  index({ commit }) {
    if (Object.keys(state.employees).length < 2) {
      return new Promise((resolve, reject) => {
        axios
          .get('/api/v1/employees')
          .then(({ data }) => {
            commit('INDEX', data);
            resolve(data);
          })
          .catch(errors => {
            this.commit('HANDLE_ERRORS', errors);
            reject(errors.response.data.errors);
          });
      });
    }
  },
  find({ dispatch }, id) {
    return new Promise(async (resolve, reject) => {
      await dispatch('index');
      let employee = state.employees[id] || null;
      employee ? resolve(this._vm.obj_clone(employee)) : reject(null);
    });
  },
  create({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post('/api/v1/employees', form)
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
      axios
        .put(`/api/v1/employees/${form.id}`, form)
        .then(({ data }) => {
          commit('UPDATE', data);
          this.dispatch('auth/fetchPermissions');
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
        .delete(`/api/v1/employees/${id}`)
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
  changePassword({ }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/employees/${form.id}/change-password`, form)
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
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