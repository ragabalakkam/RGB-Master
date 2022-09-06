window.Vue = require("vue")
import Vuex from "vuex"
Vue.use(Vuex)

import router from '../router/index'

const state = {
  app : {},
  errors : null,

  // image-viewer
  imageToView : null,

  // flags
  fullscreen : false,
  loading : true,

  // pop-ups
  popup: null,

  // inputs
  inputs: {
    select: {},
  },

  // modals
  modals: {
    'logout': { value: false, data: {} },
    'delete': { value: false, data: {} },
    'reportProblem': { value: false, data: {} },
  },
  deleteConfirmationModal: {},
};

const getters = {
  app: state => state.app,
  errors: state => state.errors,

  // image-viewer
  imageToView: state => state.imageToView,
  
  // flags
  fullscreen: state => state.fullscreen,
  loading: state => state.loading,

  // pop-ups
  popup: state => state.popup,

  // inputs
  inputs: state => state.inputs,
  select: state => state.inputs.select,

  // modals
  modals: state => state.modals,
  logoutModal: state => state.modals.logout,
  deleteModal: state => state.modals.delete,
  deleteConfirmationModal: state => state.deleteConfirmationModal,
};

const mutations = {
  SET_GLOBAL_ERRORS(state, errors) {
    state.errors = errors;
  },
  SET_APP_VARS(state, app) {
    state.app = app;
  },
  SET_IMG_TO_VIEW(state, imageToView) {
    state.imageToView = imageToView;
  },
  POP_UP(state, popup) {
    state.popup = popup;
    setTimeout(() => (state.popup = null), 750);
  },
  SET_DELETE_CONFIRMATION_MODAL(state, params) {
    state.deleteConfirmationModal = params;
  },
  SET_MODAL(state, args) {
    Vue.set(state.modals, args.name, args);
  },
  HANDLE_ERRORS(state, errors) {
    console.log(errors);
    if (errors.response) {
      switch (errors.response.status) {
        case 403:
          router.push({ name: '403' });
          break;
      }
    }
  },
  //
  SET_INPUT(state, { name, id, value }) {
    Vue.set(state.inputs[name], id, value);
  },
  SET_FULLSCREEN(state, val) {
    state.fullscreen = val;
  },
  // 
  CLEAR(state) {
    state.loading = false;
    state.errors = null;
    state.imageToView = null;
    state.popup = null;
  },
};

const actions = {
  init: async function ({ dispatch }) {
    state.loading = true;
    await dispatch("configurations/fetch");
    await dispatch("locales/init");
    state.loading = false;
  },
  popup({ commit }, popup) {
    commit("POP_UP", popup);
  },
  setDeleteConfirmationModal({ commit }, params) {
    commit("SET_DELETE_CONFIRMATION_MODAL", params);
  },
  setModal: function ({ commit }, args) {
    commit('SET_MODAL', args);
  },
  clear: function ({ commit }) {
    return new Promise(async (resolve) => {
      resolve();
    });
  },
};

const modules = {
  locales: require("../../../common/modules/locales.js").default,
  auth: require("../../../common/modules/auth.js").default,
  configurations: require("../../../common/modules/configurations.js").default,
  alerts: require("../../../common/modules/alerts.js").default,
};

export default new Vuex.Store({
  state,
  getters,
  mutations,
  actions,
  modules
});
