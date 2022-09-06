export default async function optionalAuth({ next, store }) {

  if (store.getters['auth/token'] && !store.getters['auth/user'])
    await store.dispatch('auth/init');
  
  return next();
}
