window.Vue = require("vue")
import Vuex from "vuex"
Vue.use(Vuex)

import router from '../router/index'

const state = {
  app: {},
  loading: true,
  errors: null,
  imageToView: null,
  popup: null,
  icons: {
    facebook: "facebook-f",
    messenger: "facebook-messenger",
    twitter: "twitter",
    instagram: "instagram",
    google: "google-plus",
    whatsapp: "whatsapp",
    default: "icons"
  },
  deleteConfirmationModal: {},
  modals: {
    'logout': { value: false, data: {} },
    'delete': { value: false, data: {} },
  },

  fullscreen: false,

  // inputs
  inputs: {
    account_id: {},
    select: {},
  },
};

const getters = {
  app: state => state.app,
  loading: state => state.loading,
  errors: state => state.errors,
  imageToView: state => state.imageToView,
  popup: state => state.popup,
  icons: state => state.icons,
  modals: state => state.modals,
  logoutModal: state => state.modals.logout,
  deleteModal: state => state.modals.delete,
  deleteConfirmationModal: state => state.deleteConfirmationModal,

  fullscreen: state => state.fullscreen,

  // inputs
  inputs: state => state.inputs,
  account_id: state => state.inputs.account_id,
  select: state => state.inputs.select,
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
  CLEAR(state) {
    state.loading = false;
    state.errors = null;
    state.imageToView = null;
    state.popup = null;
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
};

const actions = {
  init: async function ({ dispatch }) {
    state.loading = true;
    await dispatch("configurations/fetch");
    // await dispatch("configurations/init");
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
      await commit('notifications/CLEAR');
      await commit('user/CLEAR');
      resolve();
    });
  },
};

const modules = {
  locales: require("../../../common/modules/locales.js").default,
  images: require("../../../common/modules/images.js").default,
  auth: require("../../../common/modules/auth.js").default,
  configurations: require("../../../common/modules/configurations.js").default,
  notifications: require("../../../common/modules/notifications.js").default,
  alerts: require("../../../common/modules/alerts.js").default,
};

export default new Vuex.Store({
  state,
  getters,
  mutations,
  actions,
  modules
});
