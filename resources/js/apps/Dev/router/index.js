window.Vue = require('vue')
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import routes from './routes'
import store from '../store/index'
import middlewarePipeline from '../../../common/router/middlewarePipeline'

const router = new VueRouter({
    mode: 'history',
    routes,
})

router.beforeEach((to, from, next) => {

    if (to.meta.middleware) {
        const middleware = to.meta.middleware
        const context = {
            to,
            from,
            next,
            store
        };

        return middleware[0]({
            ...context,
            next: middlewarePipeline(context, middleware, 1)
        });
    }

    return next();
})

export default router;
