import guest from "../../../common/middlewares/guest";
import optionalAuth from "../../../common/middlewares/optionalAuth";

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
        path: "/auth/forbidden-access/:route?",
        name: "403",
        component: () => import("../../../common/views/403.vue"),
    },

    // AUTH

    {
        path: "/auth",
        component: () => import("../../../common/masters/Auth/AuthPage.vue"),
        children: [
            {
                path: "login",
                name: "login",
                component: () => import("../views/LoginForm.vue"),
                meta: { middleware: [guest] },
            },
            {
                path: "register",
                name: "register",
                component: () => import("../views/RegisterForm.vue"),
                meta: { middleware: [guest] },
            },
            {
                path: "forgot-password",
                name: "forgot-password",
                component: () => import("../views/ForgotPasswordForm.vue"),
                meta: { middleware: [guest] },
            },
            {
                path: "reset-password/:token",
                name: "reset-password",
                component: () => import("../views/ResetPasswordForm.vue"),
                meta: { middleware: [guest] },
            },
            {
                path: "verify-email/:token",
                name: "verify-email",
                component: () => import("../views/VerifyEmail.vue")
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
