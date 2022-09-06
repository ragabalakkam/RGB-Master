const state = {
  locales: {},
  translations: {},
  locale: "en",
  supportedLocales: [],
  fallback_locale: "en"
};

const getters = {
  locales: state => state.locales,
  translations: state => state.translations,
  locale: state => state.locale,
  supportedLocales: state => state.supportedLocales,
  translations: state => state.translations,
  fallback_locale: state => state.fallback_locale
};

function findLocale(id, key = "id") {
  return (
    Object.values(state.locales).find(locale => locale[key] == id) || null
  );
}

function getBrowserLocale(options = {}) {
  const defaultOptions = { countryCodeOnly: false };

  const opt = { ...defaultOptions, ...options };

  const navigatorLocale =
    navigator.languages !== undefined
      ? navigator.languages[0]
      : navigator.language;

  if (!navigatorLocale) return undefined;

  const trimmedLocale = opt.countryCodeOnly
    ? navigatorLocale.trim().split(/-|_/)[0]
    : navigatorLocale.trim();

  return trimmedLocale;
}

const mutations = {
  STORE(state, locale) {
    locale.fetched = false;
    locale.name = this._vm.store_name(locale.name);
    Vue.set(state.locales, locale.id, locale);
    state.supportedLocales.push(locale.label);
  },
  FETCH_LOCALES(state, locales) {
    state.locales = {};
    locales.forEach(locale => this.commit("locales/STORE", locale));
  },
  FETCH_TRANSLATIONS(state, { translations, label }) {
    Object.entries(translations).forEach(translation => {
      let key = translation[0],
        value = translation[1];

      if (!state.translations[key]) Vue.set(state.translations, key, {});

      state.locales[findLocale(label, "label").id].fetched = true;

      Vue.set(state.translations[key], label, value);
    });
  },
  CREATE_LOCALE(state, locale) {
    this.commit("locales/STORE", locale);
  },
  UPDATE_LOCALE(state, locale) {
    this.commit("locales/STORE", locale);
  },
  DELETE_LOCALE(state, id) {
    let locale = state.locales[id];
    Vue.delete(state.locales, id);
    state.supportedLocales = state.supportedLocales.filter(
      supportedLocale => supportedLocale !== locale.label
    );
  },
  CHANGE_LOCALE(state, label) {
    let locale = findLocale(label, "label");
    state.locale = label;
    document.documentElement.lang = locale.label;
    document.documentElement.dir = locale.dir;
    localStorage.setItem("locale", locale.label);
  },
  //
  STORE_TRANSLATION(state, translation) {
    if (!state.translations.hasOwnProperty(translation.key))
      Vue.set(state.translations, translation.key, {});

    Object.entries(translation.values).forEach(t => {
      Vue.set(state.translations[translation.key], t[0], t[1]);
    });
  },
  CREATE_TRANSLATION(state, translation) {
    this.commit("locales/STORE_TRANSLATION", translation);
  },
  UPDATE_TRANSLATION(state, translation) {
    this.commit("locales/STORE_TRANSLATION", translation);
  },
  DELETE_TRANSLATION(state, key) {
    Vue.delete(state.translations, key);
  },
  FETCH_ALL_TRANSLATIONS(state, localesTranslations) {
    Object.entries(localesTranslations).forEach(localeTranslations => {
      this.commit("locales/FETCH_TRANSLATIONS", {
        label: localeTranslations[0],
        translations: localeTranslations[1]
      });
    });
  }
};

const actions = {
  init: function ({ commit, dispatch }) {
    return new Promise(resolve => {
      dispatch("getSupportedLocales").then(() => {
        let label =
          localStorage.getItem("locale") ||
          this.state.configurations.default_locale ||
          getBrowserLocale({ countryCodeOnly: true }) ||
          process.env.MIX_APP_LOCALE;
        dispatch("change", { label }).then(() => resolve());
      });
    });
  },
  index: function ({ commit, dispatch }, forceCall = false) {
    if (forceCall || !Object.keys(state.locales).length) {
      return new Promise((resolve, reject) => {
        axios
          .get("/api/v1/locales")
          .then(({ data }) => {
            commit("FETCH_LOCALES", data);
            resolve();
          })
          .catch(errors => reject(errors));
      });
    }
  },
  findLocale: function ({ dispatch }, id) {
    return new Promise(async (resolve, reject) => {
      await dispatch("index");
      let locale = findLocale(id);
      locale ? resolve(this._vm.obj_clone(locale)) : reject(null);
    });
  },
  createLocale: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/locales", form)
        .then(({ data }) => {
          commit("CREATE_LOCALE", data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateLocale: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/locales/${form.id}`, form)
        .then(({ data }) => {
          commit("UPDATE_LOCALE", data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteLocale: function ({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/locales/${id}`)
        .then(() => {
          commit("DELETE_LOCALE", id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  factory_reset: function ({ dispatch }) {
    return new Promise((resolve, reject) => {
      axios
        .get("/api/v1/locales/factory-reset")
        .then(() => dispatch("init").then(() => resolve()))
        .catch(errors => reject(errors));
    });
  },
  getSupportedLocales: function ({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get("/api/v1/locales")
        .then(({ data }) => {
          commit("FETCH_LOCALES", data);
          resolve();
        })
        .catch(errors => reject(errors));
    });
  },
  change: function ({ commit, dispatch }, { label, showLoading = true }) {
    return new Promise(resolve => {
      if (showLoading) this.state.loading = true;
      dispatch("getLocaleTranslations", label).then(async () => {
        await commit("CHANGE_LOCALE", label);
        if (showLoading) setTimeout(() => (this.state.loading = false), 500);
        resolve();
      });
    });
  },
  getLocaleTranslations: function ({ commit, dispatch }, label) {
    return new Promise((resolve, reject) => {
      if (findLocale(label, "label").fetched) resolve();

      axios
        .get(`/${this.state.configurations.app.cache_dir}lang/${label}.json`)
        .then(({ data }) => {
          if (data.name) commit("FETCH_TRANSLATIONS", { translations: data, label });
          else throw Error('invalid format');
          resolve();
        })
        .catch(() => {
          axios
            .get(`/api/v1/translations/${label}`)
            .then(({ data }) => {
              commit("FETCH_TRANSLATIONS", { translations: data, label });
              resolve();
            })
            .catch(errors => reject(errors));
        });
    });
  },
  fetchAll: function ({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get("/api/v1/translations/all")
        .then(({ data }) => {
          commit("FETCH_ALL_TRANSLATIONS", data);
          resolve();
        })
        .catch(errors => reject(errors));
    });
  },
  //
  createTranslation: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/translations", form)
        .then(({ data }) => {
          commit("CREATE_TRANSLATION", data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateTranslation: function ({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/translations/${form.key}`, form)
        .then(({ data }) => {
          commit("UPDATE_TRANSLATION", data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteTranslation: function ({ commit }, key) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/translations/${key}`)
        .then(() => {
          commit("DELETE_TRANSLATION", key);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  backup: function ({ commit, dispatch }, { action, label = "" }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/locales/${action}/${label}`)
        .then(() => resolve())
        .catch(errors => reject(errors));
    });
  },
  setAsDefault: function ({ commit, dispatch }, label) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/configurations/set-default-locale", { label })
        .then(() => {
          this.commit("configurations/SET_DEFAULT_LOCALE", label);
          resolve();
        })
        .catch(errors => reject(errors));
    });
  }
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
};
