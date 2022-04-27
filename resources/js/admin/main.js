import '@/libs/global'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import 'ant-design-vue/dist/antd.css'

import '@/directives'
import '@/styles/app.less'
import '@/icons'
import '@/libs/error-handle'
import '@/antd'


Vue.mixin({
  beforeCreate() {
    this.$active = true
  },
  deactivated() {
    this.$active = false
  },
  activated() {
    this.$active = true
  },
})

Vue.config.productionTip = false

const app = new Vue({
  inject: {
    layout: { default: null },
  },
  router,
  store,
  render: h => h(App),
}).$mount('#admin-app')

if (process.env.NODE_ENV === 'development') {
  window.app = app
}
