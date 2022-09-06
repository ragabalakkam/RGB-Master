require('../../common/bootstrap');

window.Vue = require('vue');

import router from './router/index'
import store from './store/index'

// vue-media-query
import VueMediaQueryMixin from 'vue-media-query-mixin'
Vue.use(VueMediaQueryMixin, { framework: 'vuetify' })

// vue-spinner
Vue.component('pulse-loader', require('vue-spinner/src/PulseLoader').default)
Vue.component('clip-loader', require('vue-spinner/src/ClipLoader').default)
Vue.component('beat-loader', require('vue-spinner/src/BeatLoader').default)

// Base Components
const files = require.context('../../common/components/Base/', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

require('../../common/mixins');
require('../../common/directives');
require('../../common/prototypes');

window.store = store;

window.app = new Vue({
    store,
    router,
    render: h => h(require('../../common/App.vue').default)
}).$mount('#app');