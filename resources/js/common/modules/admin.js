const state = {
  event_logs: {},

  finances: {},
  finances_fetched: false,

  invoices: {},
  invoices_fetched: false,
  
  general_dailies: {},
  general_dailies_fetched: false,
  
  warehouse_movements: {},
  warehouse_movements_fetched: false,

  presets: {},
  preset_types: {},
  presets_fetched: false,

  backups: [],
}

const getters = {
  event_logs: state => state.event_logs,
  finances: state => state.finances,
  invoices: state => state.invoices,
  general_dailies: state => state.general_dailies,
  warehouse_movements: state => state.warehouse_movements,

  presets: state => state.presets,
  preset_types: state => state.preset_types,

  backups: state => state.backups,
}

const mutations = {
  // Event logs
  STORE_EVENT_LOG(state, event_log) {
    Vue.set(state.event_logs, event_log.id, event_log);
  },
  FETCH_EVENT_LOGS(state, event_logs) {
    event_logs.forEach(event_log => this.commit('admin/STORE_EVENT_LOG', event_log));
  },

  // finances
  STORE_FINANCE(state, finance) {
    Vue.set(state.finances, finance.id, finance);
  },
  FETCH_FINANCES(state, finances) {
    state.finances = {};
    finances.forEach(finance => this.commit('admin/STORE_FINANCE', finance));
    state.finances_fetched = true;
  },

  // Invoices
  STORE_INVOICE(state, invoice) {
    Vue.set(state.invoices, invoice.id, invoice);
  },
  FETCH_INVOICES(state, invoices) {
    state.invoices = {};
    invoices.forEach(invoice => this.commit('admin/STORE_INVOICE', invoice));
    state.invoices_fetched = true;
  },

  // Invoices
  STORE_GENERAL_DAILY(state, general_daily) {
    Vue.set(state.general_dailies, general_daily.id, general_daily);
  },
  FETCH_GENERAL_DAILIES(state, general_dailies) {
    state.general_dailies = {};
    general_dailies.forEach(gdaily => this.commit('admin/STORE_GENERAL_DAILY', gdaily));
    state.general_dailies_fetched = true;
  },

  // Warehouse movements
  STORE_WAREHOUSE_MOVEMENT(state, movement) {
    Vue.set(state.warehouse_movements, movement.id, movement);
  },
  FETCH_WAREHOUSE_MOVEMENTS(state, movements) {
    state.warehouse_movements = {};
    movements.forEach(movement => this.commit('admin/STORE_WAREHOUSE_MOVEMENT', movement));
    state.warehouse_movements_fetched = true;
  },

  // Database
  FETCH_BACKUPS(state, data) {
    state.backups = data;
  },

  FETCH_PRINT_PRESETS(state, { types, presets }) {
    types.forEach(type => Vue.set(state.preset_types, type.id, type));
    presets.forEach(preset => Vue.set(state.presets, preset.id, preset));
    state.presets_fetched = true;
  },
}

const actions = {
  fetchEventLogs: function ({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/event-logs')
        .then(({ data }) => {
          commit('FETCH_EVENT_LOGS', data);
          resolve(data);
        })
        .catch(errors => reject(errors.response.data.errors));
    });
  },

  fetchFinances: function ({ commit }) {
    return new Promise((resolve, reject) => {
      if (state.finances_fetched)
        resolve(state.finances);

      axios
        .get('/api/v1/finances')
        .then(({ data }) => {
          commit('FETCH_FINANCES', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },

  fetchInvoices: function ({ commit }) {
    return new Promise((resolve, reject) => {
      if (state.invoices_fetched)
        resolve(state.invoices);

      axios
        .get('/api/v1/invoices/get-all')
        .then(({ data }) => {
          commit('FETCH_INVOICES', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },

  fetchGeneralDailies: function ({ commit }) {
    return new Promise((resolve, reject) => {
      if (state.general_dailies_fetched)
        resolve(state.general_dailies);

      axios
        .get('/api/v1/general-dailies')
        .then(({ data }) => {
          commit('FETCH_GENERAL_DAILIES', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },

  fetchWarehouseMovements: function ({ commit }) {
    return new Promise((resolve, reject) => {
      if (state.warehouse_movements_fetched)
        resolve(state.warehouse_movements);

      axios
        .get('/api/v1/warehouses/movements')
        .then(({ data }) => {
          commit('FETCH_WAREHOUSE_MOVEMENTS', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },

  fetchPrintPresets: function ({ commit }) {
    return new Promise((resolve, reject) => {
      if (state.preset_types)
        resolve({ types: state.preset_types, presets: state.presets });

      axios
        .get('/api/v1/presets')
        .then(({ data }) => {
          commit('FETCH_PRINT_PRESETS', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  
  // database
  fetchBackups: function({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get('/api/v1/database/backups')
        .then(({ data }) => {
          data = Object.values(data);
          commit('FETCH_BACKUPS', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  backupDB: function ({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/database/backup`)
        .then(({ data }) => {
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  restoreDB: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/database/restore`, { params: form })
        .then(({ data }) => {
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  deleteBackup: function ({ commit }, filename) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/database/delete`, { params: { filename } })
        .then(() => resolve())
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  renameBackup: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/database/rename`, form)
        .then(() => resolve())
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