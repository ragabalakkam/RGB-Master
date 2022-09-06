export default function auth({ next, store }) {
  if (store.getters['auth/token']) {

    if (!store.getters['auth/user'])
      return store.dispatch('auth/init').then(() => next());

    else
      return next();
  }

  return next('/auth/login');
}
