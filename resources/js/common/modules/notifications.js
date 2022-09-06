const state = {
  notifications: {},
  quickNotificationID: null,
  orderedNotificationsIDS: [],
  unreadNotificationsCount: 0,
}

const getters = {
  notifications: state => state.notifications,
  quickNotificationID: state => state.quickNotificationID,
  quickNotification: state => state.notifications[state.quickNotificationID] || null,
  orderedNotificationsIDS: state => state.orderedNotificationsIDS,
  unreadNotificationsCount: state => state.unreadNotificationsCount
}

const mutations = {
  STORE_NOTIFICATION(state, notification) {
    if (!notification.read_at) state.unreadNotificationsCount++;
    if (!notification.content) {
      notification = { ...notification, ...notification.data };
      delete notification.data;
    }
    notification.args = {};
    notification.casts = {};
    switch (notification.content) {
      case 'emailVerified':
        notification.image = 'fab fa-angellist text-success';
        break;
      case 'passwordResetSuccess':
        notification.image = 'fas fa-check-circle text-success';
        break;
      case 'passwordResetLinkSent':
        notification.image = 'fas fa-shield-alt';
        break;
      case 'welcome':
        notification.image = 'fab fa-jenkins text-info';
        notification.args[0] = this.state.app.name;
        notification.casts[0] = 'parseName';
        break;
      case 'changedConfigurations':
        notification.image = notification.user.image;
        notification.args[0] = notification.user.name;
        notification.args[1] = Object.keys(notification.changes)[0].toString();
        notification.args[2] = Object.values(notification.changes)[0].toString();
        break;
      case 'verifyEmailLinkResent':
        notification.image = 'fad fa-sync-alt text-secondary';
        break;
      case 'DatabaseBackup':
        notification.variant = 'success-7';
        notification.image = 'fas fa-database';
        break;
      case 'AppsBackedUp':
        notification.variant = 'success';
        notification.image = 'fas fa-server';
        break;
      case 'InstalledClientApp':
      case 'AppInstallationFailed':
      case 'UpdatedClientApp':
      case 'AppUpdateFailed':
      case 'UninstalledClientApp':
      case 'AppUninstallationFailed':
        let success = ['InstalledClientApp', 'UpdatedClientApp', 'UninstalledClientApp'].includes(notification.content);
        notification.variant = success ? 'success' : 'primary';
        notification.image = success ? notification.client.image || notification.app.image || 'far fa-check-circle text-success' : 'far fa-times-octagon text-danger';
        notification.args[0] = notification.app.name;
        notification.casts[0] = 'parseName';
        notification.args[1] = notification.client.name;
        notification.casts[1] = 'parseName';
        notification.args[2] = notification.created_at || null;
        if (notification.created_at) notification.casts[2] = 'castTime';
        notification.args[3] = notification.from_version || null;
        notification.args[4] = notification.to_version || null;
        break;
      default:
        notification.image = 'fab fa-user';
    }
    Vue.set(state.notifications, notification.id, notification);
  },
  FETCH_NOTIFICATIONS_SUCCESS(state, notifications) {
    state.notifications = {};
    state.quickNotificationID = null;
    state.orderedNotificationsIDS = [];
    state.unreadNotificationsCount = 0;

    notifications.forEach(notification => {
      this.commit('notifications/STORE_NOTIFICATION', notification);
      state.orderedNotificationsIDS.push(notification.id);
    });

    if (!this.getters['preferences/doNotDisturb'])
      state.quickNotificationID = state.orderedNotificationsIDS.find(id => !state.notifications[id].read_at);
  },
  ADD_NOTIFICATION(state, notification) {
    this.commit('notifications/STORE_NOTIFICATION', notification);

    // add notification id to the begining of the ordered notifications ids array
    state.orderedNotificationsIDS.unshift(notification.id);

    // show quick notification for 2 seconds
    if (!this.getters['preferences/doNotDisturb'])
      state.quickNotificationID = notification.id;

    // alert
    this.dispatch('alerts/create', {
      content : notification.content,
      variant : notification.variant,
      timeout : notification.timeout,
      link    : notification.url,
      args    : notification.args,
      casts   : notification.casts,
    });

    // play notification sound
    this.dispatch('sounds/play', notification.sound !== false ? notification.sound || 'notification' : null);
  },
  MARK_NOTIFICATION_AS_SEEN(state, notificationID) {
    if (notificationID && !state.notifications[notificationID].read_at) {
      state.notifications[notificationID].read_at = new Date();
      state.unreadNotificationsCount--;
    } else {
      Object.values(state.notifications).filter(notification => !notification.read_at).forEach(notification => {
        state.notifications[notification.id].read_at = new Date();
        state.unreadNotificationsCount = 0;
      });
    }
  },
  CLEAR_QUICK_NOTIFICATION(state) {
    state.quickNotificationID = null;
  },
  CLEAR(state) {
    state.notifications = {};
    state.quickNotificationID = null;
    state.orderedNotificationsIDS = [];
    state.unreadNotificationsCount = 0;
  },
}

const actions = {
  fetchNotifications({ commit }) {
    if (!this.getters['auth/user']) return;
    return new Promise((resolve, reject) => {
      axios.get('/api/v1/notifications')
        .then(({ data }) => {
          commit('FETCH_NOTIFICATIONS_SUCCESS', data);
          resolve();
        })
        .catch(errors => {
          this.commit('SET_GLOBAL_ERRORS', errors);
          reject();
        });
    });
  },
  markNotificationAsSeen({ commit }, notificationID = null) {
    if (
      (!notificationID && state.unreadNotificationsCount) ||
      (notificationID && state.notifications[notificationID] && !state.notifications[notificationID].read_at)
    )
      axios.put(`/api/v1/notifications/${notificationID ? notificationID : 'all'}`)
        .then(() => commit('MARK_NOTIFICATION_AS_SEEN', notificationID));
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}