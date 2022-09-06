import master from "../middlewares/master";
import employee from "../middlewares/employee";
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
        path: "/master/forbidden-access/:route?",
        name: "403",
        component: () => import("../../../common/views/403.vue"),
    },

    // MASTER PANEL

    {
        path: "/master",
        component: () => import("../views/Master.vue"),
        children: [

            // Dashboard

            {
                path: "dashboard",
                name: "dashboard",
                component: () => import("../views/Dashboard/Dashboard.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Users

            {
                path: "users",
                name: "employees.index",
                component: () => import("../views/Users/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "users/:action(create|update)/:id?",
                name: "employees.create",
                component: () => import("../views/Users/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "users/:id",
                name: "employees.show",
                component: () => import("../views/Users/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Roles

            {
                path: "roles",
                name: "roles.index",
                component: () => import("../views/Roles/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "roles/:action(create|update)/:id?",
                name: "roles.create",
                component: () => import("../views/Roles/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "roles/:id",
                name: "roles.show",
                component: () => import("../views/Roles/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Business Types

            {
                path: "business-types",
                name: "business_types.index",
                component: () => import("../views/BusinessTypes/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "business-types/:action(create|update)/:id?",
                name: "business_types.create",
                component: () => import("../views/BusinessTypes/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "business-types/:id",
                name: "business_types.show",
                component: () => import("../views/BusinessTypes/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Clients

            {
                path: "clients",
                name: "clients.index",
                component: () => import("../views/Clients/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "clients/:action(create|update)/:id?",
                name: "clients.create",
                component: () => import("../views/Clients/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "clients/:id",
                name: "clients.show",
                component: () => import("../views/Clients/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Versions

            {
                path: "versions",
                name: "versions.index",
                component: () => import("../views/Versions/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "versions/:action(create|update)/:id?",
                name: "versions.create",
                component: () => import("../views/Versions/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "versions/:id",
                name: "versions.show",
                component: () => import("../views/Versions/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "versions/zip-files/:file",
                name: "versions.zip_files",
                component: () => import("../views/Versions/UpdateZipFile.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Apps

            {
                path: "apps",
                name: "apps.index",
                component: () => import("../views/Apps/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "apps/:action(create|update)/:id?",
                name: "apps.create",
                component: () => import("../views/Apps/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "apps/:id",
                name: "apps.show",
                component: () => import("../views/Apps/Show.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Locations

            {
                path: "locations/countries",
                name: "locations.countries.index",
                component: () => import("../views/Locations/Countries/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/countries/:action(create|update)/:id?",
                name: "locations.countries.create",
                component: () => import("../views/Locations/Countries/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/states",
                name: "locations.states.index",
                component: () => import("../views/Locations/States/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/states/:action(create|update)/:id?",
                name: "locations.states.create",
                component: () => import("../views/Locations/States/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/cities",
                name: "locations.cities.index",
                component: () => import("../views/Locations/Cities/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/cities/:action(create|update)/:id?",
                name: "locations.cities.create",
                component: () => import("../views/Locations/Cities/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/districts",
                name: "locations.districts.index",
                component: () => import("../views/Locations/Districts/Index.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },
            {
                path: "locations/districts/:action(create|update)/:id?",
                name: "locations.districts.create",
                component: () => import("../views/Locations/Districts/Create.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Locales

            {
                path: "locales",
                name: "locales",
                component: () => import("../views/Locales/Locales.vue"),
                meta: {
                    middleware: [auth, employee]
                }
            },

            // Configurations

            {
                path: "configurations",
                name: "configurations",
                component: () => import("../views/Configurations/Configurations.vue"),
                meta: {
                    middleware: [auth, employee]
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
