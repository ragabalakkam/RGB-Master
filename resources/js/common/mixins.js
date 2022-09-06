window.Vue = require('vue')
import { CastTime } from './methods'

Vue.mixin({
  methods: {

    getCurrentDateTime: function () {
      var tzoffset = (new Date()).getTimezoneOffset() * 60000;
      var localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -8);
      return localISOTime;
    },

    // translations

    getLocale: function () {
      return this.$store.getters['locales/locale'] || this.$store.getters['locales/fallback_locale'];
    },
    $t: function (key, attrs = {}, locale = this.getLocale()) {
      let value = key;
      try {
        value = this.$store.getters['locales/translations'][value][locale];
        Object.entries(attrs).forEach(attr => value = value.replaceAll(`{${attr[0]}}`, attr[1]));
      } catch (e) {
        console.warn(`[WARN] ${key} : is not defined in ${locale.toUpperCase()} locale`)
      }
      return value;
    },

    // text-emphasis

    ucFirst: function (text) {
      return text[0].toUpperCase() + text.substr(1).toLowerCase();
    },
    capitalize: function (string, splitter = ' ') {
      string = string.replace(' ', '-').split('-');
      string.forEach(word => this.ucFirst(word));
      return string.join(splitter);
    },
    allCaps: function (string) {
      return string.toUpperCase();
    },
    firstName: function (name) {
      let fname = name.split(' ')[0];
      return fname[0].toUpperCase() + fname.substr(1).toLowerCase()
    },
    abbr: function (sentence, charsLimit = 100, separator = ' ', ellipsis = true) {
      return sentence.split(separator).reduce((acc, word) => acc.length < charsLimit ? acc + separator + word : acc, '') + (ellipsis && sentence.length > charsLimit ? ' ..' : '');
    },
    camelize: function (str) {
      return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
        return index === 0 ? word.toLowerCase() : word.toUpperCase();
      }).replace(/\s+/g, '');
    },

    // casts

    parseAddress: function (address, isFull = false) {
      if (!address) return null;

      let result = "",
        country = this.countries[address[0]],
        state = this.states[address[1]],
        city = this.cities[address[2]],
        district = this.districts[address[3]];

      if (address[5]) result += `${address[5]} `;
      if (address[4]) result += `${this.$t('streetX', { x: address[4] })} , `;
      if (district) result += `${this.$t('districtX', { x: this.parseName(district.name) })} , `;
      if (city) result += `${this.$t('cityX', { x: this.parseName(city.name) })} , `;
      if (state) result += `${this.$t('stateX', { x: this.parseName(state.name) })} , `;
      if (isFull && country) result += `${this.parseName(country.name)} , `;
      if (address[6]) result += `${this.$t('postalCodeX', { x: address[6] })}`;

      return result;
    },
    parseName: function (name, locale = null) {
      if (!name) return null;
      const _locale = locale || this.getLocale();
      return name.hasOwnProperty(_locale) ? name[_locale] : name;
    },
    castTime: function (time) {
      if (!time) return null;

      const t = this.$t;
      time = CastTime(time);

      let string = '';
      if (time.day) string += t('CastTime.' + time.day) + ' ';
      if (time.month) string += !['today', 'yesterday'].includes(time.day) ? `( ${time.d} ${t('CastTime.' + time.month)} ) ` : '';
      if (time.time) string += time.time + ' ';
      if (time.am_pm) string += t('CastTime.' + time.am_pm);

      return string;
    },
    parseTime: function (time) {
      let am = true;
      time = time.split(':');
      time.pop();
      if (time[0] >= 12) {
        am = false;
        time[0] = time[0] > 12 ? time[0] - 12 : time[0];
      }
      return `${time.join(':')} ${this.$t('CastTime.' + (am ? 'AM' : 'PM'))}`;
    },
    formatDate: function (date) {
      var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2)
        month = '0' + month;
      if (day.length < 2)
        day = '0' + day;

      return [year, month, day].join('-');
    },
    parseImg: function (model) {
      return model && model.image ? `/storage/${typeof model.image == 'string' ? model.image : model.image.src}` : null;
    },
    rnd: function (num, decimals = null) {
      if (!decimals) decimals = this.$store.getters['cashier/decimals'];
      decimals = Math.pow(10, decimals);
      return Math.round((num + Number.EPSILON) * decimals) / decimals;
    },

    calc_ratio: function (value, ratio) {
      return value * (ratio / 100);
    },
    include_tax: function (value, ratio) {
      return value * (1 + (ratio / 100));
    },
    exclude_tax: function (value, ratio) {
      return value / (1 + (ratio / 100));
    },
    tax_exculded: function (value, ratio) {
      return value * ratio / (100 + ratio);
    },
    castNumber: function (num) {
      if (!num) return 0;
      return num.toString().includes('.') ? this.round(num) : `${num}.00`;
    },
    num: function (num) {
      return (num || 0).toFixed(2);
    },

    // colors

    parseColor: function (color) {
      return color ? color.startsWith('#') || color.startsWith('rgb') ? color : `var(--bs-${color})` : null;
    },
    isDark: function (color) {

      if (!color) return null;

      var r, g, b, hsp;

      if (color.match(/^rgb/)) {
        color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
        r = color[1];
        g = color[2];
        b = color[3];
      }
      else {
        color = +("0x" + color.slice(1).replace(color.length < 5 && /./g, '$&$&'));
        r = color >> 16;
        g = color >> 8 & 255;
        b = color & 255;
      }

      hsp = Math.sqrt(
        0.299 * (r * r) +
        0.587 * (g * g) +
        0.114 * (b * b)
      );

      return hsp <= 150; // 127.5;
    },

    // uncategorized

    PopUp: function (msg) {
      let bg = document.createElement('div'),
        msgContainer = document.createElement('div');
      bg.className = 'position-absolute position-top-left vw-100 vh-100 bg-black-8 d-flex-center';
      bg.style.zIndex = '99999999';
      msgContainer.className = 'rounded-edges text-light bg-black h3 px-4 py-2';
      msgContainer.innerHTML = msg;
      bg.appendChild(msgContainer);
      document.body.appendChild(bg);
      setTimeout(() => document.body.removeChild(bg), 700);
    },
    popupOnClick: function (popup, duration = 1) {
      if (!popup)
        popup = { msg: 'success' }
      this.$store.commit('SET_POP_UP', popup)
      setTimeout(() => this.$store.commit('SET_POP_UP', null), duration * 1000)
    },
    splitNum: function (number) {
      let numberArray = number.toString().split('.');
      return {
        number: numberArray[0],
        decimals: numberArray[1],
      };
    },
    getFormData: function (form) {
      let formdata = new FormData();
      Object.entries(form).forEach(entry => {
        formdata.append(entry[0], entry[1]);
      });
      return formdata;
    },
    img: function (src) {
      return src ? `/storage/${src}` : null;
    },
    confirmDelete: function (model, action, attr = null, params = null, callback = () => { }) {
      this.$store.dispatch('setModal', { name: 'delete', value: true, model, action, attr, params, callback });
    },
    redirect: function (name, params = {}) {
      return this.$router.push({ name });
    },
    relatedRoute: function () {
      let user = this.$store.getters['auth/user'];
      if (user) {
        switch (user.role)
        {
          case 'employee':
            if (this.can('adminPanel.access'))
              return '/master/dashboard';

          case 'customer':
            return '/client/dashboard';
        }
      }
      return '/';
    },
    smartRedirect: function (path) {
      let route = this.$router.match(path);
      return route && route.name != '404' ? this.$router.push(path) : window.location.replace(path);
    },
    round: function (number, decimals = null) {
      if (!decimals) decimals = this.$store.getters['cashier/decimals'];
      decimals = Math.pow(10, decimals);
      return Math.round((number + Number.EPSILON) * decimals) / decimals;
    },
    any_in_array(arr1, arr2) {
      return !arr1.length || arr2.filter(el => arr1.includes(el)).length;
    },
    visit(uri, new_tab = true) {
      window.open(uri, new_tab ? 'blank' : '');
    },

    optional: function (text) {
      return `${text} (${this.$t('optional')})`;
    },
    can: function (actions = []) {
      return this.any_in_array(typeof actions == 'string' ? [actions] : actions, this.$store.getters['auth/permissions']);
    },
    hasGroup: function (name) {
      return this.$store.getters['auth/permissions'].find(p => p.includes(`${name}${name.includes('/') ? '' : '/'}`));
    },
    hasGroups: function (arr) {
      return arr.filter(el => this.hasGroup(el)).length;
    },
    hasModule: function (name) {
      try {
        return this.$store.getters['configurations/modules'][name];
      } catch (e) { return false };
    },

    withoutTrashed: function (obj) {
      return Object.values(obj).filter(d => !d.deleted_at);
    },

    // objects
    obj_length: function (object) {
      return Object.keys(object).length;
    },
    obj_clone: function (obj) {
      let result = JSON.parse(JSON.stringify(obj));
      if (result.image) delete result.image;
      return result;
    },

    // files
    downloadURI: function (uri, name = null) {
      var link = document.createElement("a");
      if (name) link.download = name;
      link.href = uri;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },

    createOrUpdate: function (str, action = null, hasAl = false) {
      const t = this.$t;
      if (!action) action = this.$route.params.action;
      let string = `${hasAl ? t('ال') : ''}${t(str)}`;
      return t(action == 'update' ? 'updateX' : 'createX', { attr: string });
    },

    // enable / disable fullscreen
    setFullscreen: function (val = true) {
      this.$store.commit('SET_FULLSCREEN', val);
    },

    // testing
    consoleLog: function (x) {
      console.log(x);
    },
  },
})