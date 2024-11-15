import {createRouter, createWebHistory} from "vue-router";
import dashboard from "./pages/dashboard.vue";
import plans from "./pages/plans/index.vue";
import plugins from "./pages/plugins/index.vue";
import features from "./pages/features/index.vue";
import subscriptions from "./pages/subscriptions/index.vue";

window.Global.basePath = '/' + window.Global.path;

let routerBasePath = window.Global.basePath + '/';

if (window.Global.path === '' || window.Global.path === '/') {
    routerBasePath = '/';
    window.Global.basePath = '';
}

const router = createRouter({
    history: createWebHistory(routerBasePath),
    routes: [
        {path: '/', redirect: '/dashboard'},

        {
            path: '/dashboard',
            name: 'Dashboard',
            component: dashboard,
        },
        {
            path: '/plans',
            name: 'Plans',
            component: plans,
        },
        {
            path: '/plugins',
            name: 'Plugins',
            component: plugins,
        },
        {
            path: '/features',
            name: 'Features',
            component: features,
        },
        {
            path: '/subscriptions',
            name: 'Subscriptions',
            component: subscriptions,
        },
    ]
})

router.beforeEach((to, from, next) => {
    document.title = "La Subscription | " +to.name;
    next();
});
export default router;
