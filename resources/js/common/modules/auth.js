import Cookies from 'js-cookie'

const state =
{
  user: null,
  token: Cookies.get('token'),
  permissions: [],
  errors: [],
}

const getters =
{
  user: state => state.user,
  token: state => state.token,
  emailVerified: state => state.user && !!state.user.email_verified_at,
  errors: state => state.errors,
  role: state => state.user.role,
  permissions: state => state.permissions,
  id: state => state.user ? state.user.id : null,
}

const mutations =
{
  LOGIN_SUCCESS(state) {
    state.errors = [];
    state.token = Cookies.get('token');
  },
  FETCH_USER_SUCCESS(state, user) {
    state.email_verified_at = user.email_verified_at;
    if (this.state.notifications) this.state.notifications.unreadNotificationsCount = user.unread_notifications;
    state.user = user;
  },
  USER_UPDATE_SUCCESS(state, user) {
    state.user = { ...state.user, ...user };
  },
  CHANGE_PROFILE_PICTURE(state, img) {
    state.user.image = img;
  },
  SET_ERRORS(state, errors) {
    this.commit('SET_GLOBAL_ERRORS', errors);
  },
  REGENERATE_CODE_SUCCESS(state, code) {
    state.user.code = code;
  },
  FETCH_IMAGES_SUCCESS(state, images) {
    state.images = images;
  },
  EMAIL_VERIFIED(state) {
    let now = new Date();
    Cookies.set('email_verified_at', now);
    state.email_verified_at = now;
  },
  CLEAR(state) {
    Cookies.remove('token');
    Cookies.remove('remember_me');
    state.user = null;
    state.token = null;
    state.role = null;
    state.errors = [];
  },
  FETCH_PERMISSIONS(state, permissions) {
    state.permissions = permissions;
  },
}

const actions =
{
  init({ dispatch }) {
    return new Promise((resolve) => {
      dispatch('fetchUser').then(() => resolve());
    });
  },

  login({ commit }, credentials) {
    return new Promise((resolve, reject) => {
      axios.post('/api/v1/auth/login', credentials)
        .then(({ data }) => {
          commit('LOGIN_SUCCESS');
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  register({ commit }, data) {
    return new Promise((resolve, reject) => {
      axios.post('/api/v1/auth/register', data)
        .then(() => {
          commit('LOGIN_SUCCESS');
          resolve();
        })
        .catch(errors => {
          commit('SET_ERRORS', errors);
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  fetchUser: async function ({ commit, dispatch }) {
    await axios.get('/api/v1/auth/user')
      .then(async ({ data }) => {
        if (!data.id) dispatch('logout');
        else {
          await dispatch('fetchPermissions');
          await commit('FETCH_USER_SUCCESS', data);
          await dispatch('startListening');
        }
      })
      .catch(errors => {
        commit('SET_ERRORS', errors);
        this.commit('HANDLE_ERRORS', errors);
        dispatch('logout');
      });
  },
  logout({ commit }) {
    return new Promise(async (resolve, reject) => {
      this.state.loading = true;
      await axios.post('/api/v1/auth/logout')
        .then(() => {
          commit('CLEAR');
          if (!this.state.app.offline && state.user) this.dispatch('auth/stopListening');
          this.dispatch('clear');
        })
        .catch(errors => {
          commit('SET_ERRORS', errors);
          this.commit('HANDLE_ERRORS', errors);
          reject(errors);
        });
      // this.state.loading = false;
      resolve();
    });
  },

  // verify email
  verifyEmail({ commit }, token) {
    return new Promise((resolve, reject) => {
      axios.post('/api/v1/auth/verify-email', { token })
        .then(({ data }) => resolve(data))
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  resendVerificationLink({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get('/api/v1/auth/resend-verification-link')
        .then(() => resolve())
        .catch(errors => {
          commit('SET_ERRORS', errors);
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },

  // change password
  sendResetPasswordLink({ commit }, form = { email: state.user.email }) {
    return new Promise((resolve, reject) => {
      axios.post('/api/v1/auth/requset-password-reset', form)
        .then(response => resolve(response))
        .catch(errors => {
          commit('SET_ERRORS', errors);
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  changePassword({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios.put('/api/v1/auth/change-password', form)
      .then(() => resolve())
      .catch(errors => {
        this.commit('HANDLE_ERRORS', errors);
        reject(errors.response.data.errors);
      });
    });
  },

  // edit profile
  editProfile({ commit }, form) {
    return new Promise((resolve, reject) => {
      axios.put('/api/v1/auth/update', form)
        .then(({ data }) => {
          commit('USER_UPDATE_SUCCESS', data);
          resolve();
        })
        .catch(errors => {
          commit('SET_ERRORS', errors);
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors);
        });
    });
  },
  changeProfilePicture({ commit }, image) {
    return new Promise((resolve, reject) => {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      };
      let formdata = new FormData();
      formdata.append('image', image);
      axios
        .post('/api/v1/auth/change-profile-picture', formdata, config)
        .then(({ data }) => {
          commit('CHANGE_PROFILE_PICTURE', data);
          resolve();
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },

  // permissions
  fetchPermissions({ commit }) {
    return new Promise((resolve, reject) => {
      axios
        .get(`/api/v1/user/permissions`)
        .then(({ data }) => {
          commit('FETCH_PERMISSIONS', data);
          resolve(data);
        })
        .catch(errors => {
          this.commit('HANDLE_ERRORS', errors);
          reject(errors.response.data.errors)
        });
    });
  },
  
  // listening to pusher channels
  startListening({ commit }) {
    try {
      window.Echo.private(`App.User.${state.user.id}`)
        .notification(notification => {
          switch(notification.content)
          {
            case 'emailVerified':
              commit('EMAIL_VERIFIED');
              break;
            case 'InstalledClientApp':
            case 'UninstalledClientApp':
              this.commit('clients/SET_APP_INSTALLED_AT', { client_id: notification.client.id, ...notification.app_client });
              break;
          }
          this.commit('notifications/ADD_NOTIFICATION', notification);
        });
    } catch (e) { console.log(e) }
  },
  stopListening({ }) {
    window.Echo.leave(`App.User.${state.user.id}`);
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}