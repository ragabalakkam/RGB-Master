import optionalAuth from "../../../common/middlewares/optionalAuth";
import auth from "../../../common/middlewares/auth";

export default [

    // REDIRECT

    {
        path: "/redirect",
        name: "redirect",
        component: () => import("../../../common/views/Redirect.vue"),
        meta: { middleware: [optionalAuth] },
    },

    // 403 FORBIDDEN

    {
        path: "/forbidden-access/:route?",
        name: "403",
        component: () => import("../../../common/views/403.vue"),
    },
    
    // DEFAULT VIEWS

    {
        path: "/",
        component: () => import("../../../common/masters/Default/DefaultMaster.vue"),
        children: [

            // HOME PAGE
            {
                path: "",
                name: "home-page",
                component: () => import("../views/HomePage.vue"),
                meta: {
                    middleware: [optionalAuth],
                }
            },

            // PROFILE PAGE
            {
                path: "profile",
                name: "profile",
                component: () => import("../views/ProfilePage.vue"),
                meta: {
                    middleware: [auth],
                }
            },

            // Apps
            {
                path: "apps",
                name: "apps.index",
                component: () => import("../../Client/views/Apps/Index.vue"),
            },
            {
                path: "apps/:id",
                name: "apps.show",
                component: () => import("../../Client/views/Apps/Show.vue"),
            },
        ]
    },

    // 404 NOT FOUND

    {
        path: "*",
        name: "404",
        component: () => import("../../../common/views/404.vue"),
    }
];
