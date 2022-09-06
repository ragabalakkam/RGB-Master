export default function employee({ next, store, to }) {

  const user = store.getters['auth/user']
  const user_permissions = store.getters['auth/permissions'];
  const routes_permissions = store.getters['permissions/routes_permissions'];

  if (user && user.role == 'employee' && any_in_array(routes_permissions[to.name], user_permissions))
    return next();

  return next({ name: '403', params: { route: to.name }});
}

function any_in_array(arr1, arr2) {
  return !arr1.length || arr2.filter(el => arr1.includes(el)).length;
}