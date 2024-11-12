// import { createApp } from 'vue';
import {createApp} from 'vue/dist/vue.esm-bundler';
import ExampleComponent from "./components/ExampleComponent.vue";

const app = createApp({});
app.component('example-component', ExampleComponent);
app.mount('#app');
