const state = {
  roles: {},
  fetching_roles: false,
  fetched_roles: false,
  permissions_groups: {},
  permissions: {},
  highest_priority: 0,
  routes_permissions: {
    "dashboard"                   : ['adminPanel.access'],

    "configurations"              : ['configurations.show', 'configurations.update'],

    'locations.countries.index'   : ['locations.countries.index'],
    'locations.countries.create'  : ['locations.countries.store', 'locations.countries.update'],
    'locations.countries.show'    : ['locations.countries.show'],

    'locations.states.index'      : ['locations.states.index'],
    'locations.states.create'     : ['locations.states.store', 'locations.states.update'],
    'locations.states.show'       : ['locations.states.show'],

    'locations.cities.index'      : ['locations.cities.index'],
    'locations.cities.create'     : ['locations.cities.store', 'locations.cities.update'],
    'locations.cities.show'       : ['locations.cities.show'],

    'locations.districts.index'   : ['locations.districts.index'],
    'locations.districts.create'  : ['locations.districts.store', 'locations.districts.update'],
    'locations.districts.show'    : ['locations.districts.show'],

    "apps.index"                  : ['apps.index', 'apps.store', 'apps.update', 'apps.delete'],
    "apps.create"                 : ['apps.store', 'apps.update'],
    "apps.show"                   : [],

    "versions.index"              : ['versions.index', 'versions.store', 'versions.update', 'versions.delete'],
    "versions.create"             : ['versions.store', 'versions.update'],
    "versions.show"               : [],

    "business_types.index"        : ['business_types.index', 'business_types.store', 'business_types.update', 'business_types.delete'],
    "business_types.create"       : ['business_types.store', 'business_types.update'],
    "business_types.show"         : [],

    "clients.index"               : ['clients.index', 'clients.store', 'clients.update', 'clients.delete'],
    "clients.create"              : ['clients.store', 'clients.update'],
    "clients.show"                : [],

    "clientApps.index"            : ['clientApps.index', 'clientApps.store', 'clientApps.update', 'clientApps.delete'],
    "clientApps.create"           : ['clientApps.store', 'clientApps.update'],
    "clientApps.show"             : [],

    "departments.index"           : ['departments.index', 'departments.store', 'departments.update', 'departments.delete'],
    "departments.create"          : ['departments.store', 'departments.update'],
    "departments.show"            : [],

    "employees.index"             : ['employees.index', 'employees.store', 'employees.update', 'employees.delete'],
    "employees.create"            : ['employees.store', 'employees.update'],
    "employees.show"              : [],

    "roles.index"                 : ['roles.index', 'roles.store', 'roles.update', 'roles.delete'],
    "roles.create"                : ['roles.store', 'roles.update'],
    "roles.show"                  : [],
  },
}

const getters = {
  roles: state => state.roles,
  permissions_groups: state => state.permissions_groups,
  permissions: state => state.permissions,
  highest_priority: state => state.highest_priority,
  routes_permissions: state => state.routes_permissions,
}

const mutations = {
  STORE_ROLE_INTO_STATE(state, role) {
    Vue.set(state.roles, role.id, role);
  },
  STORE_PERMISSION_INTO_STATE(state, permission) {
    Vue.set(state.permissions, permission.id, permission);
  },
  STORE_PERMISSION_GROUP_INTO_STATE(state, permissions_group) {
    Vue.set(state.permissions_groups, permissions_group.id, permissions_group);
    Object.values(permissions_group.permissions).forEach(permission => {
      this.commit('permissions/STORE_PERMISSION_INTO_STATE', permission);
    });
  },
  INDEX(state, { roles, permissions_groups, highest_priority }) {
    roles.forEach(role => {
      this.commit('permissions/STORE_ROLE_INTO_STATE', role);
    });
    permissions_groups.forEach(group => {
      this.commit('permissions/STORE_PERMISSION_GROUP_INTO_STATE', group);
    });
    state.highest_priority = highest_priority;
    state.fetching_roles = false;
    state.fetched_roles = true;
  },
  CREATE(state, role) {
    this.commit('permissions/STORE_ROLE_INTO_STATE', role);
  },
  UPDATE(state, role) {
    this.commit('permissions/STORE_ROLE_INTO_STATE', role);
  },
  DELETE(state, id) {
    Vue.delete(state.roles, id);
  },
}

const actions = {
  index({ commit }) {
    if (!state.fetched_roles && !state.fetching_roles) {
      state.fetching_roles = true;
      return new Promise((resolve, reject) => {
        axios
          .get('/api/v1/roles')
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
      let role = state.roles[id] || null;
      role && role.priority <= state.highest_priority ? resolve(this._vm.obj_clone(role)) : reject(null);
    });
  },
  show({ commit }, id) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/roles/${id}`)
        .then(({ data }) => {
          commit('STORE_ROLE_INTO_STATE', data);
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
      axios
        .post('/api/v1/roles', form)
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
      axios
        .put(`/api/v1/roles/${form.id}`, form)
        .then(({ data }) => {
          commit('UPDATE', data);
          this.dispatch('auth/fetchPermissions');
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
        .delete(`/api/v1/roles/${id}`)
        .then(() => {
          commit('DELETE', id);
          this.commit('departments/ROLE_DELETED', id);
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