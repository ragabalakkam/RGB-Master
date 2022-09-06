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
        path: "/dev/forbidden-access/:route?",
        name: "403",
        component: () => import("../../../common/views/403.vue"),
    },
    
    // DEV VIEWS

    {
        path: "/dev",
        component: () => import("../../../common/masters/Default/DefaultMaster.vue"),
        children: [

            // TEST
            {
                path: "test",
                name: "test",
                component: () => import("../views/Test.vue"),
                meta: {
                    middleware: [optionalAuth],
                }
            },

            // SNIPPETS CREATOR
            {
                path: "snippets/create",
                name: "snippets.create",
                component: () => import("../views/SnippetsCreator.vue"),
                meta: {
                    middleware: [optionalAuth],
                }
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
