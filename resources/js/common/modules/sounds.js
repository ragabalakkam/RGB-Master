const state = {
  notification: new Audio('/sounds/notification.mp3'),
  error: new Audio('/sounds/error.ogg'),
  timeouts: {},
}

const getters = {
  //
}

const mutations = {
  PLAY(state, name) {
    if (!state.hasOwnProperty(name))
      return console.log(`[ERROR] ${name} is not registered as a sound.`)
    state[name].play();
  },
  STOP(state, name) {
    if (!state.hasOwnProperty(name))
      return console.log(`[ERROR] ${name} is not registered as a sound.`)
    state[name].pause();
    state[name].currentTime = 0;
    if (state.timeouts[name]) clearTimeout(state.timeouts[name]);
  },
}

const actions = {
  play({ commit }, name = null) {
    if (!this.getters['preferences/doNotDisturb'] && name) commit('PLAY', name);
  },
  stop({ commit }, name = null) {
    if (!state[name].paused && !state[name].ended && name) commit('STOP', name);
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}