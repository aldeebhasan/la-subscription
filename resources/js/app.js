// import { createApp } from 'vue';
import {createApp} from 'vue/dist/vue.esm-bundler';
import VueSweetalert2 from 'vue-sweetalert2';
import axios from 'axios';
import router from './routes.js';
import base from './base.js';
import '../css/styles.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import {getCurrentInstance} from "vue";
import Icon from "@/components/Icon.vue";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['accept'] = 'application/json';
const app = createApp({});
app.component("icon", Icon);

app.use(router)
    .use(VueSweetalert2)
    .mixin(base)
    .mount('#app');


app.config.globalProperties.$http = axios.create();

app.config.globalProperties.successAlert = (title, text) => {
    getCurrentInstance().proxy.$swal.fire({
        title: title, text: text, icon: "success"
    });
};
app.config.globalProperties.errorAlert = (app, title, text) => {
    app.proxy.$swal.fire({
        title: title, text: text, icon: "error"
    });
};
app.config.globalProperties.confirmAlert = (app, callback) => {
    app.proxy.$swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#504f4f",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};

const globals = app.config.globalProperties;
export {globals}
