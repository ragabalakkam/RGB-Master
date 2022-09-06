window.Vue = require('vue')
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import routes from './routes'
import store from '../store/index'
import middlewarePipeline from '../../../common/router/middlewarePipeline'

// propagate metadata recursively to child routes
function propagateMetadata(routes, meta) {
    routes.forEach(route => {
        if (route.meta === undefined) {
            route.meta = meta
        }
        if (route.children !== undefined) {
            propagateMetadata(route.children, route.meta)
        }
    })
}
propagateMetadata(routes, {})

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
