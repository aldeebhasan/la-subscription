import {createRouter, createWebHistory} from "vue-router";
import dashboard from "./pages/dashboard.vue";
import groups from "./pages/groups/index.vue";
import groupCreate from "./pages/groups/create.vue";
import plans from "./pages/plans/index.vue";
import planCreate from "./pages/plans/create.vue";
import pluginCreate from "./pages/plugins/create.vue";
import plugins from "./pages/plugins/index.vue";
import featureCreate from "./pages/features/create.vue";
import features from "./pages/features/index.vue";
import subscriptions from "./pages/subscriptions/index.vue";
import subscriptionShow from "./pages/subscriptions/show.vue";

window.Global.basePath = '/' + window.Global.path;

let routerBasePath = window.Global.basePath + '/';

if (window.Global.path === '' || window.Global.path === '/') {
    routerBasePath = '/';
    window.Global.basePath = '';
}

const router = createRouter({
    history: createWebHistory(routerBasePath), routes: [
        {
            path: '/', redirect: '/dashboard'
        }, {
            path: '/dashboard', name: 'Dashboard', component: dashboard
        }, {
            path: '/groups', name: 'Groups', component: groups
        }, {
            path: '/groups/:id', name: 'Create Group', component: groupCreate
        }, {
            path: '/plans', name: 'Plans', component: plans
        }, {
            path: '/plans/:id', name: 'Create Plan', component: planCreate
        }, {
            path: '/plugins', name: 'Plugins', component: plugins,
        }, {
            path: '/plugins/:id', name: 'Create Plugin', component: pluginCreate
        }, {
            path: '/features', name: 'Features', component: features,
        }, {
            path: '/features/:id', name: 'Create Feature', component: featureCreate
        }, {
            path: '/subscriptions', name: 'Subscriptions', component: subscriptions,
        }, {
            path: '/subscriptions/:id', name: 'Subscription Detail', component: subscriptionShow,
        },
    ]
})

router.beforeEach((to, from, next) => {
    document.title = "La Subscription | " + to.name;
    next();
});
export default router;
