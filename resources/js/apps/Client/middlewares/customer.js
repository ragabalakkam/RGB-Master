export default function customer({ next, store, to }) {

  const user = store.getters['auth/user']

  if (user && user.role == 'customer')
    return next();

  return next({ name: '403', params: { route: to.name }});
}