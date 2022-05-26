import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import http_client from './services/http_client.js';
import VueLazyload from 'vue-lazyload'

import preview from 'vue-photo-preview'
import 'vue-photo-preview/dist/skin.css'
Vue.use(preview)

import error_img from './assets/load-loading.gif';
import loading_img from './assets/load-loading.gif';
Vue.use(VueLazyload, {
    preLoad: 1.3,
    error: error_img,
    loading_img: loading_img,
    attempt: 1
})
Vue.config.productionTip = false
Vue.prototype.$http = http_client;
Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
