const state = {
  countries: {},
  fetched_countries: false,

  states: {},
  fetched_states: false,

  cities: {},
  fetched_cities: false,

  districts: {},
  fetched_districts: false,
};

const getters = {
  countries: state => state.countries,
  states: state => state.states,
  cities: state => state.cities,
  districts: state => state.districts,
};

const mutations = {
  COUNTRY_STORE(state, country) {
    country.name = this._vm.store_name(country.name);
    Vue.set(state.countries, country.id, country);
  },
  COUNTRY_INDEX(state, countries) {
    countries.forEach(country => {
      this.commit("locations/COUNTRY_STORE", country);
    });
    state.fetched_countries = true;
  },
  COUNTRY_CREATE(state, country) {
    this.commit("locations/COUNTRY_STORE", country);
  },
  COUNTRY_UPDATE(state, country) {
    this.commit("locations/COUNTRY_STORE", country);
  },
  COUNTRY_DELETE(state, id) {
    Object.values(state.states).filter(state => state.country_id == id).forEach(state => {
      this.commit('locations/STATE_DELETE', state.id);
    });
    Vue.delete(state.countries, id);
  },
  //
  STATE_STORE(state, s) {
    s.name = this._vm.store_name(s.name);
    Vue.set(state.states, s.id, s);
  },
  STATE_INDEX(state, states) {
    states.forEach(s => this.commit("locations/STATE_STORE", s));
    state.fetched_states = true;
  },
  STATE_CREATE(state, s) {
    this.commit("locations/STATE_STORE", s);
  },
  STATE_UPDATE(state, s) {
    this.commit("locations/STATE_STORE", s);
  },
  STATE_DELETE(state, id) {
    this.commit('locations/REPLACE_USED_CITYS', { id });
    Vue.delete(state.states, id);
  },
  //
  CITY_STORE(state, city) {
    city.name = this._vm.store_name(city.name);
    Vue.set(state.cities, city.id, city);
  },
  CITY_INDEX(state, cities) {
    cities.forEach(city => {
      this.commit("locations/CITY_STORE", city);
    });
    state.fetched_cities = true;
  },
  CITY_CREATE(state, city) {
    this.commit("locations/CITY_STORE", city);
  },
  CITY_UPDATE(state, city) {
    this.commit("locations/CITY_STORE", city);
  },
  CITY_DELETE(state, id) {
    Vue.delete(state.cities, id);
  },
  //
  DISTRICT_STORE(state, district) {
    district.name = this._vm.store_name(district.name);
    Vue.set(state.districts, district.id, district);
  },
  DISTRICT_INDEX(state, districts) {
    districts.forEach(district => {
      this.commit("locations/DISTRICT_STORE", district);
    });
    state.fetched_districts = true;
  },
  DISTRICT_CREATE(state, district) {
    this.commit("locations/DISTRICT_STORE", district);
  },
  DISTRICT_UPDATE(state, district) {
    this.commit("locations/DISTRICT_STORE", district);
  },
  DISTRICT_DELETE(state, id) {
    Vue.delete(state.districts, id);
  },
};

const actions = {
  getCountries({ commit }) {
    if (!state.fetched_countries) {
      return new Promise((resolve, reject) => {
        axios
          .get("/api/v1/countries")
          .then(({ data }) => {
            commit("COUNTRY_INDEX", data);
            resolve(data);
          })
          .catch(errors => {
            this.commit('HANDLE_ERRORS', errors);
            reject(errors.response.data.errors);
          });
      });
    }
  },
  findCountry({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      let country = state.countries[id] || null;
      if (country) resolve(this._vm.obj_clone(country));

      axios.get(`/api/v1/countries/${id}`)
        .then(({ data }) => {
          commit("COUNTRY_STORE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createCountry({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/countries", form)
        .then(({ data }) => {
          commit("COUNTRY_CREATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateCountry({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/countries/${form.id}`, form)
        .then(({ data }) => {
          commit("COUNTRY_UPDATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteCountry({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/countries/${id}`)
        .then(() => {
          commit("COUNTRY_DELETE", id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  getStates({ commit }) {
    if (!state.fetched_states) {
      return new Promise((resolve, reject) => {
        axios
          .get("/api/v1/states")
          .then(({ data }) => {
            commit("STATE_INDEX", data);
            resolve(data);
          })
          .catch(errors => {
            console.log(errors);
            reject(errors.response.data.errors);
          });
      });
    }
  },
  findState({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      let s = state.states[id] || null;
      if (s) resolve(this._vm.obj_clone(s));

      axios.get(`/api/v1/states/${id}`)
        .then(({ data }) => {
          commit("STATE_STORE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createState({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/states", form)
        .then(({ data }) => {
          commit("STATE_CREATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateState({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/states/${form.id}`, form)
        .then(({ data }) => {
          commit("STATE_UPDATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteState({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/states/${id}`)
        .then(() => {
          commit("STATE_DELETE", id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  getCities({ commit }) {
    if (!state.fetched_cities) {
      return new Promise((resolve, reject) => {
        axios
          .get("/api/v1/cities")
          .then(({ data }) => {
            commit("CITY_INDEX", data);
            resolve(data);
          })
          .catch(errors => {
            this.commit('HANDLE_ERRORS', errors);
            reject(errors.response.data.errors);
          });
      });
    }
  },
  findCity({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      let city = state.cities[id] || null;
      if (city) resolve(this._vm.obj_clone(city));

      axios.get(`/api/v1/cities/${id}`)
        .then(({ data }) => {
          commit("CITY_STORE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createCity({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/cities", form)
        .then(({ data }) => {
          commit("CITY_CREATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateCity({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/cities/${form.id}`, form)
        .then(({ data }) => {
          commit("CITY_UPDATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteCity({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/cities/${id}`)
        .then(() => {
          commit("CITY_DELETE", id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  getDistricts({ commit }) {
    if (!state.fetched_districts) {
      return new Promise((resolve, reject) => {
        axios
          .get("/api/v1/districts")
          .then(({ data }) => {
            commit("DISTRICT_INDEX", data);
            resolve(data);
          })
          .catch(errors => {
            this.commit('HANDLE_ERRORS', errors);
            reject(errors.response.data.errors);
          });
      });
    }
  },
  findDistrict({ commit }, id) {
    return new Promise(async (resolve, reject) => {
      let district = state.districts[id] || null;
      if (district) resolve(this._vm.obj_clone(district));

      axios.get(`/api/v1/districts/${id}`)
        .then(({ data }) => {
          commit("DISTRICT_STORE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  createDistrict({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/v1/districts", form)
        .then(({ data }) => {
          commit("DISTRICT_CREATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  updateDistrict({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios
        .put(`/api/v1/districts/${form.id}`, form)
        .then(({ data }) => {
          commit("DISTRICT_UPDATE", data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  deleteDistrict({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`/api/v1/districts/${id}`)
        .then(() => {
          commit("DISTRICT_DELETE", id);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  //
  fetch({ commit }) {
    return new Promise(async (resolve, reject) => {
      axios.get(`/${this.state.configurations.app.cache_dir}locations.json`)
        .then(({ data }) => {
          commit('COUNTRY_INDEX', data.countries);
          commit('STATE_INDEX', data.states);
          resolve();
        })
        .catch(() => {
          axios
            .get('/api/v1/locations')
            .then(({ data }) => {
              commit('COUNTRY_INDEX', data.countries);
              commit('STATE_INDEX', data.states);
              resolve();
            })
            .catch(errors => reject(errors.response.data.errors));
        });
    });
  },
  //
  fetchStates: function ({ commit }, id) {
    if (!id) return;
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/locations/country/${id}/states`)
        .then(({ data }) => {
          commit('STATE_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  updateCities: function ({ commit }, id) {
    if (!id) return;
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/locations/state/${id}/cities`)
        .then(({ data }) => {
          commit('CITY_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  updateDistricts: function ({ commit }, id) {
    if (!id) return;
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/locations/city/${id}/districts`)
        .then(({ data }) => {
          commit('DISTRICT_INDEX', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
};
