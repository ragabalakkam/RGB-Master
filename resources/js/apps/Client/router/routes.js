import optionalAuth from "../../../common/middlewares/optionalAuth";
import auth from "../../../common/middlewares/auth";
import guest from "../../../common/middlewares/guest";
import customer from "../middlewares/customer";

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
        path: "/client/forbidden-access/:route?",
        name: "403",
        component: () => import("../../../common/views/403.vue"),
    },

    // Auth
    {
        path: "/client/register",
        name: "register",
        component: () => import("../views/Auth/Register.vue"),
        meta: { middleware: [guest] }
    },
    {
        path: "/client/login",
        name: "login",
        component: () => import("../views/Auth/Login.vue"),
        meta: { middleware: [guest] }
    },
    {
        path: "/client/forgot-password",
        name: "forgot_password", 
        component: () => import("../views/Auth/ForgotPassword.vue"),
        meta: { middleware: [guest] }
    },

    // DEFAULT VIEWS

    {
        path: "/client",
        component: () => import("../../Client/views/Client.vue"),
        meta: { middleware: [auth, customer] },
        children: [

            // Dashboard
            {
                path: "dashboard",
                name: "dashboard",
                component: () => import("../views/Dashboard.vue"),
            },

            // Profile page
            {
                path: "profile",
                name: "profile",
                component: () => import("../views/Profile/ProfilePage.vue"),
            },
            {
                path: "reset-password",
                name: "reset_password",
                component: () => import("../views/Profile/ResetPasswordPage.vue"),
            },

            // Apps
            {
                path: "apps",
                name: "apps.index",
                component: () => import("../views/Apps/Index.vue"),
                meta: { middleware: [auth] }
            },
            {
                path: "apps/:id",
                name: "apps.show",
                component: () => import("../views/Apps/Show.vue"),
                meta: { middleware: [auth] }
            },

            // Organizations
            {
                path: "organizations",
                name: "organizations.index",
                component: () => import("../views/Organizations/Index.vue"),
            },
            {
                path: "organizations/:action(create|update)/:org_id?",
                name: "organizations.create",
                component: () => import("../views/Organizations/Create.vue"),
            },
            {
                path: "organizations/:org_id",
                name: "organizations.show",
                component: () => import("../views/Organizations/Show.vue"),
            },

            // Organization Apps
            {
                path: "organizations-apps",
                name: "organization.apps.index",
                component: () => import("../views/Organizations/Apps/Index.vue"),
            },
            {
                path: "organizations-apps/:action(create|update)",
                name: "organization.apps.create",
                component: () => import("../views/Organizations/Apps/Create.vue"),
            },
            {
                path: "organizations-apps/:id",
                name: "organization.apps.show",
                component: () => import("../views/Organizations/Apps/Show.vue"),
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
