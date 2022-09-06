const state = {}

const getters = {}

const mutations = {}

const actions = {
  prepareFormWithImg: function ({}, { url, form }) {
    return new Promise((resolve) => {
      const config = { headers: { 'content-type': 'multipart/form-data' } };
      let formdata = new FormData();

      Object.entries(form).forEach(entry => {
        switch (typeof entry[1]) {
          case 'object':
            if (entry[1] && ((entry[0] != 'file' && entry[0] != 'database_file' && entry[0] != 'image') || entry[1].en)) entry[1] = JSON.stringify(entry[1]);
            break;
          case 'boolean':
            entry[1] = entry[1] ? 1 : 0;
            break;
        }
        formdata.append(entry[0], entry[1]);
      });

      resolve(axios.post(url, formdata, config));
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