require('./bootstrap');
window.Vue = require('vue').default;

import router from "./router/index.js";
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify);

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('app-vue', require('./App.vue').default);

const app = new Vue({
    el: '#app',
    router,
    vuetify: new Vuetify({iconfont: 'mdi'}),
});
