// import { createApp } from 'vue';
import {createApp} from 'vue/dist/vue.esm-bundler';
import axios from 'axios';
import router from './routes.js';
import base from './base.js';
import '../css/styles.css';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['accept'] = 'application/json';
const app = createApp({});

app.config.globalProperties.$http = axios.create();
app.use(router)
    .mixin(base)
    .mount('#app');

const globals = app.config.globalProperties;
export {globals}
