const state = {
  alerts: {},
  default_timeout: 10,
}

const getters = {
  alerts: state => state.alerts,
  default_timeout: state => state.default_timeout,
}

const mutations = {
  CREATE_ALERT(state, alert) {
    alert.id = Math.ceil(Math.random() * 1000000);
    Vue.set(state.alerts, alert.id, alert);
  },
  CLOSE_ALERT(state, alertID) {
    Vue.delete(state.alerts, alertID);
  },
}

const actions = {
  create({commit}, alert) {
    commit('CREATE_ALERT', alert);
  },
  close({commit}, alertID) {
    commit('CLOSE_ALERT', alertID);
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}