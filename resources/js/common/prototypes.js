window.Vue = require('vue')

Vue.prototype.isJson = function (str) {
  try { JSON.parse(str); }
  catch (e) { return false; }
  return true;
}

Vue.prototype.store_name = function (name) {
  return name && this.isJson(name) && !name.en ? JSON.parse(name) : name;
}

Vue.prototype.obj_clone = function (obj) {
  return JSON.parse(JSON.stringify(obj));
}

Vue.prototype.getCurrentDateTime = function () {
  var tzoffset = (new Date()).getTimezoneOffset() * 60000;
  var localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -8);
  return localISOTime;
}
