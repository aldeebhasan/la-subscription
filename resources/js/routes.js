import {createRouter, createWebHistory} from "vue-router";
import dashboard from "./pages/dashboard.vue";
import plans from "./pages/plans/index.vue";

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
            name: 'dashboard',
            component: dashboard,
        },
        {
            path: '/plans',
            name: 'plans',
            component: plans,
        },
    ]
})

export default router;
