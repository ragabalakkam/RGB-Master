export default function master({ next, to, store }) {

  const user = store.getters['auth/user'];

  if (user && user.role == 'master')
    return next();

  return next({ name: '403', params: { route: to.name } });
}