// import { createApp } from 'vue';
import {createApp} from 'vue/dist/vue.esm-bundler';
import ExampleComponent from "./components/ExampleComponent.vue";
import axios from 'axios';
import router from './routes.js';
import base from './base.js';
import dashboard from "./pages/dashboard.vue";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const app = createApp({});
app.config.globalProperties.$http = axios.create();
app.component('example-component', ExampleComponent);
app.component('dashboard', dashboard);

app.use(router)
    .mount('#app');
    // .mixin(base)
